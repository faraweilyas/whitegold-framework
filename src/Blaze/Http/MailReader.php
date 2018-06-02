<?php
    namespace Blaze\Http;

    /**
    * whiteGold - mini PHP Framework
    *
    * @package whiteGold
    * @author Farawe iLyas <faraweilyas@gmail.com>
    * @link http://faraweilyas.me
    *
    * MailReader Class
    */
    class MailReader
    {
        // imap server connection
        public $conn;
        // inbox storage and inbox message count
        public $inbox;
        private $msg_cnt;
        // email login credentials
        private $server = '';
        private $user   = '';
        private $pass   = '';
        // adjust according to server settings
        private $port   = 993; 

        // connect to the server and get the inbox emails
        function __construct ()
        {
            $this->connect();
            $this->inbox();
        }

        // close the server connection
        function close ()
        {
            $this->inbox    = [];
            $this->msg_cnt  = 0;
            imap_close($this->conn);
        }

        // open the server connection
        // the imap_open function parameters will need to be changed for the particular server
        // these are laid out to connect to a Dreamhost IMAP server
        function connect ()
        {
            $this->conn = imap_open('{'.$this->server.'/notls}', $this->user, $this->pass);
        }

        // move the message to a new folder
        function move ($msg_index, $folder='INBOX.Processed')
        {
            // move on server
            imap_mail_move($this->conn, $msg_index, $folder);
            imap_expunge($this->conn);
            // re-read the inbox
            $this->inbox();
        }

        // get a specific message (1 = first email, 2 = second email, etc.)
        function get ($msg_index=NULL)
        {
            if (count($this->inbox) <= 0) 
                return [];
            elseif ( ! is_null($msg_index) && isset($this->inbox[$msg_index]))
                return $this->inbox[$msg_index];
            return $this->inbox;
        }

        // read the inbox
        function inbox ()
        {
            $this->msg_cnt = imap_num_msg($this->conn);
            $in = [];
            for ($i = 1; $i <= $this->msg_cnt; $i++)
            {
                $in[] = [
                    'index'     => $i,
                    'header'    => imap_headerinfo($this->conn, $i),
                    'body'      => imap_body($this->conn, $i),
                    'structure' => imap_fetchstructure($this->conn, $i)
                ];
            }
            $this->inbox = $in;
        }

        function test ()
        {
            $mailbox    = new MailReader;
            $email      = $mailbox->get(159);
            echo '<tt><pre>'.var_export($email, TRUE).'</pre></tt>';
            echo "<!DOCTYPE html><html><head><title>Email</title></head><body>";
            echo "<table><thead><tr><td>ID</td><td>Header</td></tr></thead><tbody>";
            echo $total = count($mailbox->inbox);
            $all = 9;
            for ($i = $total; $i >= 0; $i--)
            { 
                $email = $mailbox->inbox[$i];
                echo "<tr>";
                echo "<td>".$email['index']."</td>";
                echo "<td>".htmlentities($email['header']->subject)."</td>";
                echo "</tr>";
                if ($i == ($total - $all)) break;
            }
            // foreach ($mailbox->inbox as $email)
            // {
            //     echo "<tr>";
            //     echo "<td>".$email['index']."</td>";
            //     // echo "<td>".htmlentities($email['body'])."</td>";
            //     echo "<td>".htmlentities($email['header']->subject)."</td>";
            //     echo "</tr>";
            //     $id++; 
            //     if ($id == $all) break;
            // }
            echo "</tbody></table></body></html>";
            $mailbox->close();
        }
    }
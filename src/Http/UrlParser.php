<?php

namespace Blaze\Http;

/**
 * UrlParser Class
 */
class UrlParser
{
	/**
	 * Link to parse.
	 * @var string
	 */
	public $link;
	
	/**
	 * Link to parse.
	 * @var string
	 */
	public $scheme;
	
	/**
	 * Link to parse.
	 * @var string
	 */
	public $host;

	/**
	 * Link to parse.
	 * @var string
	 */
	public $port;

	/**
	 * Link to parse.
	 * @var string
	 */
	public $user;

	/**
	 * Link to parse.
	 * @var string
	 */
	public $pass;

	/**
	 * Link to parse.
	 * @var string
	 */
	public $path;

	/**
	 * Link to parse.
	 * @var string
	 */
	public $query;

	/**
	 * Link to parse.
	 * @var string
	 */
	public $fragment;

    /**
     * Constructor sets url link to parse
     * @param string $link
     */
    public function __construct(string $link)
    {
    	$this->link = $link;
        $this->parseUrl();
    }

	/**
	 * Parse the url.
	 * @return UrlParser
	 */
	public function parseUrl() : UrlParser
	{
		$url = parse_url($this->link);
		// port, user, pass
    	$this->scheme 	= $url['scheme'] 	?? "";
    	$this->host 	= $url['host'] 		?? "";
    	$this->port 	= $url['port'] 		?? "";
    	$this->user 	= $url['user'] 		?? "";
    	$this->pass 	= $url['pass'] 		?? "";
    	$this->path 	= $url['path'] 		?? "";
    	$this->query 	= $url['query'] 	?? "";
    	$this->fragment = $url['fragment'] 	?? "";
		return $this;
	}

	/**
	 * Get subdomain from link and returns string or array
	 * @param int $tldLevel eg .com or .com.ng
	 * @return string
	 */
	final public function getSubDomain(int $tldLevel=1) : string
	{
		$host 		= explode('.', $this->getHost());
		$subdomains = array_mpop($host, $tldLevel + 1);
		return joinArray($subdomains, '.');
	}

	/**
	 * Get subdomains from link and returns string or array
	 * @param int $tldLevel eg .com or .com.ng
	 * @return array
	 */
	final public function getSubDomains(int $tldLevel=1) : array
	{
		$host = explode('.', $this->getHost());
		return array_mpop($host, $tldLevel + 1);
	}

	/**
	 * Is there scheme.
	 * @return bool
	 */
	final public function isThereScheme() : bool
	{
		return !empty($this->getScheme()) ? TRUE : FALSE;
	}

	/**
	 * Is there host.
	 * @return bool
	 */
	final public function isThereHost() : bool
	{
		return !empty($this->getHost()) ? TRUE : FALSE;
	}

	/**
	 * Is there host.
	 * @param string $host
	 * @return bool
	 */
	final public function isHostTheSame(string $host) : bool
	{
		return ($this->getHost() == $host) ? TRUE : FALSE;
	}

	/**
	 * Is there port.
	 * @return bool
	 */
	final public function isTherePort() : bool
	{
		return !empty($this->getPort()) ? TRUE : FALSE;
	}

	/**
	 * Is there fragment.
	 * @return bool
	 */
	final public function isThereQuery() : bool
	{
		return !empty($this->getQuery()) ? TRUE : FALSE;
	}

	/**
	 * Is there fragment.
	 * @return bool
	 */
	final public function isThereFragment() : bool
	{
		return !empty($this->getFragment()) ? TRUE : FALSE;
	}

	/**
	 * Get scheme.
	 * @return string
	 */
	final public function getScheme() : string
	{
		return $this->scheme ?? "";
	}

	/**
	 * Get host.
	 * @param bool $full
	 * @return string
	 */
	final public function getHost(bool $full=FALSE) : string
	{
		$port 		= $this->isTherePort() ? ":".$this->getPort() : $this->getPort();
		$scheme 	= $this->isThereScheme() ? $this->getScheme()."://" : $this->getScheme();
		$host 		= $this->host ?? "";
		$fullHost 	= "{$scheme}{$host}{$port}";
		return ($full) ? $fullHost : $host;
	}

	/**
	 * Get port.
	 * @return string
	 */
	final public function getPort() : string
	{
		return $this->port ?? "";
	}

	/**
	 * Get user.
	 * @return string
	 */
	final public function getUser() : string
	{
		return $this->user ?? "";
	}

	/**
	 * Get pass.
	 * @return string
	 */
	final public function getPass() : string
	{
		return $this->pass ?? "";
	}

	/**
	 * Get path.
	 * @return string
	 */
	final public function getPath() : string
	{
		return $this->path ?? "";
	}

	/**
	 * Get query.
	 * @return string
	 */
	final public function getQuery() : string
	{
		return $this->query ?? "";
	}

	/**
	 * Get fragment.
	 * @return string
	 */
	final public function getFragment() : string
	{
		return $this->fragment ?? "";
	}
}

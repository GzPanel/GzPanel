<?php
	
	abstract class ResponseException extends \Exception
	{
		// Base exception class
	}
	
	class failedNodeCheck extends ResponseException
	{
		const FAILED_NODE_CHECK = 1;
	}


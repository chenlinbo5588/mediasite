<?php

class Rsa {

/**
	 +----------------------------------------------------------
 *   RSA数据加密和解密类
	 +----------------------------------------------------------
 */
	//define("BCCOMP_LARGER", 1); 
	protected $BCCOMP_LARGER = 1;

/**
	 +----------------------------------------------------------
 *   数据加密函数
	 +----------------------------------------------------------
 *  @param string $message 需要加密的数据
 *  @param string $public_key rsa加密公钥
 *  @param string $modulus rsa加密模
	 +----------------------------------------------------------
 *  @return string 加密过后字符串
	 +----------------------------------------------------------
 */
	public function rsa_encode($message, $public_key, $modulus) {          
		if(strlen($public_key) < 2 || $modulus < 2)
			assert(0);
		if("0x"==strtolower(substr($public_key,0,2))){
			$public_key = substr($public_key,2,(strlen($public_key)-2));
			$public_key = $this->hex2dec($public_key);
		}
		if("0x"==strtolower(substr($modulus,0,2))){
			$modulus = substr($modulus,2,(strlen($modulus)-2));
			$modulus = $this->hex2dec($modulus);
		}
		$message = $this->binary_to_number2($message);
		$intResult = $this->pow_mod2($message,$public_key,$modulus);
		$hexResult = bin2hex($this->number_to_binary2($intResult));
		return $hexResult;
	}

/**
	 +----------------------------------------------------------
 *   数据解密函数
	 +----------------------------------------------------------
 *  @param string $sign 加密的数据字符串
 *  @param string $prikey rsa加密私钥
 *  @param string $modulus rsa加密模
	 +----------------------------------------------------------
 *  @return string 数据原型字符串
	 +----------------------------------------------------------
 */
	public function rsa_decode($sign, $prikey, $modulus) {
		if(strlen($prikey) < 2 || $modulus < 2)
			assert(0);
		if("0x"==strtolower(substr($prikey,0,2))){
			$prikey = substr($prikey,2,(strlen($prikey)-2));
			$prikey = $this->hex2dec($prikey);
		}
		if("0x"==strtolower(substr($modulus,0,2))){
			$modulus = substr($modulus,2,(strlen($modulus)-2));
			$modulus = $this->hex2dec($modulus);
		}
		$sign = $this->hex2dec($sign);

		$msg = $this->pow_mod2($sign,$prikey,$modulus);
		$msg = $this->number_to_binary2($msg);
		return $msg;
		/*
		$intSign = bin2int(hex2bin($sign));
		$intExponent = bin2int(hex2bin($exponent));
		$intModulus = bin2int(hex2bin($modulus));
		$intResult = $this->powmod($intSign, $intExponent, $intModulus);
		$hexResult = bin2hex(int2bin($intResult));       
		return $hexResult;
		*/
	}

	protected function pow_mod($p, $q, $r)
	{
		// Extract powers of 2 from $q   
	  
		$factors = array();   
	  
		$div = $q;   
	  
		$power_of_two = 0;   
	  
		while(bccomp($div, "0") == $this->BCCOMP_LARGER)   
	  
		{   
	  
			$rem = bcmod($div, 2);   
	  
			$div = bcdiv($div, 2);   
	  
		   
	  
			if($rem) array_push($factors, $power_of_two);   
	  
			$power_of_two++;   
	  
		}   
	  
	  
		// Calculate partial results for each factor, using each partial result as a   
	  
		// starting point for the next. This depends of the factors of two being   
	  
		// generated in increasing order.   
	  
		$partial_results = array();   
	  
		$part_res = $p;   
	  
		$idx = 0;   
	  
		foreach($factors as $factor)   
	  
		{   
	  
			while($idx < $factor)   
	  
			{   
	  
				$part_res = bcpow($part_res, "2");   
	  
				$part_res = bcmod($part_res, $r);   
	  
	  
				$idx++;   
	  
			}   
	  
			   
	  
			array_push($partial_results, $part_res);   
	  
		}   
		// Calculate final result   
	  
		$result = "1";   
	  
		foreach($partial_results as $part_res)   
	  
		{   
	  
			$result = bcmul($result, $part_res);   
	  
			$result = bcmod($result, $r);   
	  
		}   
		return $result;   
	}

	protected function pow_mod2($p, $q, $r)
	{   
		// Extract powers of 2 from $q   
		//var_dump(bccomp($div, "0"));
		//exit();
		$factors = array();   
		$div = $q;
		$power_of_two = 0;   
		//var_dump($div);
		//exit();
		while(bccomp($div, "0") == $this->BCCOMP_LARGER){   
			$rem = bcmod($div,2);   
			$div = bcdiv($div,2);
			if($rem) array_push($factors, $power_of_two);   
			$power_of_two++;   
		}
		//var_dump($factors);
		//exit();
		// Calculate partial results for each factor, using each partial result as a   
		// starting point for the next. This depends of the factors of two being   
		// generated in increasing order.   
		$partial_results = array();   
		$part_res = $p;   
		//var_dump($part_res);
		//exit();
		$idx = 0;   
		foreach($factors as $factor){   
			while($idx < $factor){   
				$part_res = bcpow($part_res, "2");   
				$part_res = bcmod($part_res, $r);   
				$idx++;   
			}   
			array_push($partial_results, $part_res);   
		}   
		// Calculate final result   
		$result = "1";   
		foreach($partial_results as $part_res){   
			$result = bcmul($result, $part_res);   
			$result = bcmod($result, $r);   
		}   
		return $result;
	}

	public function hex2dec($data)
	{    
		if("0x"==strtolower(substr($data,0,2))){
			$data = substr($data,2,(strlen($data)-2));
		}
		$base = "16";
		$radix = "1";
		$result = "0";
		for($i = strlen($data) - 1; $i >= 0; $i--){
			$digit = hexdec($data{$i});
			$part_res = bcmul($digit, $radix);
			$result = bcadd($result, $part_res);
			$radix = bcmul($radix, $base);
		}
		return $result;
	}
	protected function binary_to_number2($data)
	{
		$base = "256";
		$radix = "1";
		$result = "0";
		for($i = strlen($data) - 1; $i >= 0; $i--){
			$digit = ord($data{$i});
			$part_res = bcmul($digit, $radix);
			$result = bcadd($result, $part_res);
			$radix = bcmul($radix, $base);
		}
		return $result;
	}
	protected function number_to_binary2($number)
	{
		$base = "256";   
		$result = "";   
		$div = $number;   
		while($div > 0){   
			$mod = bcmod($div, $base);   
			$div = bcdiv($div, $base);   
			$result = chr($mod) . $result;   
		}   
		return $result;
	}   
	protected function binary_to_number($data)   
	{    
		$base = "256";   
		$radix = "1";   
		$result = "0";   
		for($i = strlen($data) - 1; $i >= 0; $i--){   
			$digit = ord($data{$i});   
			$part_res = bcmul($digit, $radix);   
			$result = bcadd($result, $part_res);   
			$radix = bcmul($radix, $base);   
		}   
		return $result;   
	}     
	protected function number_to_binary($number, $blocksize)   
	{
		$base = "256";   
		$result = "";   
		$div = $number;   
		while($div > 0){   
			$mod = bcmod($div, $base);   
			$div = bcdiv($div, $base);   
			$result = chr($mod) . $result;   
		}   
		//return $result;
		return str_pad($result, $blocksize, "\x00", STR_PAD_LEFT);
	}
	//--   
	  
	// Function to add padding to a decrypted string   
	  
	// We need to know if this is a private or a public key operation [4]   
	  
	//--   
	  
	protected function add_PKCS1_padding($data, $isPublicKey, $blocksize)   
	  
	{   
	  
		$pad_length = $blocksize - 3 - strlen($data);   
		//var_dump($pad_length);
		//exit();
		if($isPublicKey)     
		{   
			$block_type = "\x02";   
	  
		   
	  
			$padding = "";   
	  
			for($i = 0; $i < $pad_length; $i++)   
	  
			{   
	  
				$rnd = mt_rand(1, 255);   
	  
				$padding .= chr($rnd);   
	  
			}   
			//var_dump($padding);
			//exit();
	  
		}   
	  
		else  
	  
		{   
	  
			$block_type = "\x01";   
	  
			$padding = str_repeat("\xFF", $pad_length);   
	  
		}   
		//var_dump("\x00" . $block_type . $padding . "\x00" . $data);
		//exit();
	  
		return "\x00" . $block_type . $padding . "\x00" . $data;   
	  
	}   
	  
	  
	//--   
	  
	// Remove padding from a decrypted string   
	  
	// See [4] for more details.   
	  
	//--   
	  
	protected function remove_PKCS1_padding($data, $blocksize)   
	  
	{   
		//var_dump(strlen($data) == $blocksize);
		//assert(1==2);
		//echo strlen($data)."==".$blocksize;
		//exit();  
		assert(strlen($data) == $blocksize);   
	  
		$data = substr($data, 1);   
	  
	  
		// We cannot deal with block type 0   
	  
		if($data{0} == '\0')   
	  
			die("Block type 0 not implemented.");   
	  
	  
		// Then the block type must be 1 or 2    
	  
		assert(($data{0} == "\x01") || ($data{0} == "\x02"));   
	  
	  
		// Remove the padding   
	  
		$offset = strpos($data, "\0", 1);   
	  
		return substr($data, $offset + 1);   
	  
	}   
	  

	//-------------原旧作------------
	protected function powmod2($String,$pow,$mod){
		//$pow = bin2int(hex2bin($pow));	
		//var_dump($pow);
		//exit();
		$UTFlen=strlen($String);
		$x="";
		$y="";
		$z="";
		$ReturnString="";
		//415031
		//13447399033
		for ($i=0;$i<$UTFlen;$i++) {
			$x=strval(ord(substr($String,$i,1)));
			//var_dump(bcpow($x,$pow));
			//var_dump($mod);
			//var_dump($pow);
			//var_dump(bcpow($x,"415031"));
			//var_dump(pow_mod2($x,$pow,$mod));
			//exit();
			//$y = bcpow($x,$pow);

			//$y = bcmod(bcpow($x,$pow),$mod);
			$y=$this->pow_mod2($x,$pow,$mod);
			//var_dump($y);
			//exit();
			//$z = sprintf("%03X",$y);
			$ReturnString .= $y." ";
		}
		//var_dump($ReturnString);
		//exit();
		return $ReturnString;
	}
	protected function powmod3($String,$pow,$mod){
		//$UTFlen=strlen($String);
		$x="";
		$y="";
		$z="";
		$ReturnString="";
		$arr = explode(" ",trim($String));
		$UTFlen = count($arr);
		for ($i=0;$i<$UTFlen;++$i) {
			//$x=(substr($String,$i,3));
			//$x = hexdec($x);
			$x = trim($arr[$i]);
			if("" == $x)
				continue;
			//$y = bcmod(bcpow($x,$pow),$mod);
			$y=$this->pow_mod2($x,$pow,$mod);

			$z = chr($y);
			$ReturnString .= $z;
			//var_dump($z);
			//exit();
			//$x=ord(substr($String,$i,1));
			//$y = bcmod(bcpow($x,$pow),$mod);
			//$z = sprintf("%03X",$y);
			//$ReturnString .= $z;
		}
		return $ReturnString;
	}

	protected function powmod($num, $pow, $mod){
	  $result = '1';
	  //var_dump(!bccomp(bcmod($pow, '2'), '1'));
	  //exit();
	  do {
		  //var_dump(!bccomp(bcmod($pow, '2'), '1'));
		  //exit();
		  if (!bccomp(bcmod($pow, '2'), '1')) {
			  //1*224
			  //224%2773
			  //var_dump(bcmod("224",$mod));
			  //exit();
			  $result = bcmod(bcmul($result, $num), $mod);
		  }
		  $num = bcmod(bcpow($num, '2'), $mod);
		  $pow = bcdiv($pow, '2');
	  } while (bccomp($pow, '0'));
	  return $result;
	}

}
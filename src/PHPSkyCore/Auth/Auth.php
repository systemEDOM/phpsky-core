<?php

namespace PHPSkyCore\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Mockery\CountValidator\Exception;
use Firebase\JWT\SignatureInvalidException;

class Auth
{
	public static $userModel;
	public static $fieldEmail = "email";
	
	/**
     * Verify user into database
	 * @param array $credentials Array is the the credentials where the first elemetn is equals to email and the second is equals to password
     * @return boolean
     */
	public static function verifyUser($credentials)
	{
		$user = self::$userModel::where(self::$fieldEmail, $credentials[0])->first();

		if ($user)
			return password_verify($credentials[1], $user->password) ? $user : false;
		return false;
	}

	/**
     * Retun data to later convert to jwt
	 * @param User $user is the model who wants to login
     * @return Array
     */
	public static function setData($user = null)
	{
		$tokenId    = base64_encode(random_bytes(32));
        $issuedAt   = time();
        $notBefore  = $issuedAt + 10;
        $expire     = $notBefore + 7200;
        $serverName = APP['url_base'];

        $data = [
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss'  => $serverName,       // Issuer
            'nbf'  => $notBefore,        // Not before
            'exp'  => $expire,           // Expire
            'user' => $user
        ];

        return $data;
	}

	/**
     * Attempt or login user from credentials
	 * @param array $credentials Array is the the credentials where the first elemetn is equals to email and the second is equals to password
     * @return boolean
     */
	public static function attempt($credentials = array(), $userModel = null, $fieldEmail = null)
	{
		if ($userModel != null)
			self::$userModel = $userModel;
		if ($fieldEmail != null)
			self::$fieldEmail = $fieldEmail;
		try {
        	if($user = self::verifyUser($credentials))	{

	            $secretKey = base64_decode(SECRET_KEY);
				$jwt = JWT::encode(self::setData($user), $secretKey, ALGORITHM);

				setcookie("jwt", $jwt);

				return true;
			}
		} catch (ExpiredException $e) {
			if(isset($_COOKIE['jwt']) || !empty($_COOKIE['jwt'])) return redirect('/');
			exit;
		}
	}

	/**
     * Attempt or login user from jwt token
	 * @param String $jwt is the jwt token for login user
     * @return boolean
     */
	public static function attemptFromJWT($jwt)
	{
		try {
			$secretKey = base64_decode(SECRET_KEY);
			$jwtDecode = JWT::decode($jwt, $secretKey, array(ALGORITHM));
			setcookie("jwt", $jwt);
			return true;
		} catch (SignatureInvalidException $e) {
			//echo $e->getMessage();
			return false;
		}
	}

	/**
     * Attempt or login user from model
	 * @param User $user is the model user for login
     * @return boolean
     */
	public static function attemptFromModel($user)
	{
		try {
			if ($user != null) {
				$secretKey = base64_decode(SECRET_KEY);
				$jwt = JWT::encode(self::setData($user), $secretKey, ALGORITHM);

				setcookie("jwt", $jwt);

				return true;
			}
			return false;
		} catch (ExpiredException $e) {
			if(isset($_COOKIE['jwt']) || !empty($_COOKIE['jwt'])) return redirect('/');
			exit;
		}
	}

	/**
	 * Logout user from the app
	 * @return void
	 */
	public static function logout()
	{
		if(isset($_COOKIE['jwt'])) {
			setcookie("jwt", "");
		} else {
			redirect('/');
		}
	}

	/**
	 * Get user authenticated
	 * @return User
	 */
	public static function getAuth()
	{
		if(isset($_COOKIE['jwt']) && !empty($_COOKIE['jwt'])) {
			try {
				
				JWT::$leeway = 60;
				
        		$secretKey = base64_decode(SECRET_KEY); 
				$decodeDataArray = JWT::decode($_COOKIE['jwt'], $secretKey, array(ALGORITHM));

				$json = json_encode($decodeDataArray, true);
				$accesJson = json_decode($json);

				return $accesJson;

			} catch (ExpiredException $e) {
				redirect('/login');
			} catch (Exception $e) {
				redirect('/login');
			}
		}
	}

	/**
	 * Get the password hashed
	 * @param [String] $password
	 * @param [HASH TYPE] $hash
	 * @return String
	 */
	public static function hash($password, $hash = PASSWORD_BCRYPT)
	{
		return password_hash($password, $hash);
	}
}
<?php 
// necessary imports 
require_once '../vendor/autoload.php';
require_once 'database.php';
// algorithm 
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\Algorithm\HS256;
// building the jws 
use Jose\Component\Signature\JWSBuilder; 
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Core\JWK;
use Jose\Component\Core\JWKSet;
// validation 
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Checker; 
// - header validation
use Jose\Component\Checker\HeaderCheckerManager;
use Jose\Component\Checker\AlgorithmChecker;
use Jose\Component\Signature\JWSTokenSupport;
// - claim validation 
use Jose\Component\Checker\ClaimCheckerManager; 

class JWT {
    private $serializer; 
    private $algManager; 
    private $key; 
    private $db; 

    // constructor
    public function __construct(){
        $this->serializer = new CompactSerializer(); 
        $this->algManager = new AlgorithmManager([
            new HS256()
        ]);
        $this->key = $this->JWK(); 
        $this->db = new Database(); 
    }

    // key (JWK) generator helper function
    public function JWK() {
        return JWKFactory::createOctKey(
            1024, // Size in bits of the key. We recommend at least 128 bits.
            [
                'alg' => 'HS256', // This key must only be used with the HS256 algorithm
                'use' => 'sig'    // This key is used for signature/verification operations only
            ]
        );
    }

    // payload generator helper function
    private function generatePayload($id, $sem, $firstname) {
        return json_encode([
            'iat' => time(), // issued at
            'nbf' => time(), // not before 
            'exp' => time() + 7100, // expiration
            'iss' => 'Montage', // issuer 
            'sem' => $sem, // seminar
            'id' => $id, // sid
            'name' => $firstname
        ]);
    }

    // create JWS 
    public function generateJWS($id, $sem, $firstname) {
        // instantiate JWS builder
        $jwsBuilder = new JWSBuilder($this->algManager);
        $jws = $jwsBuilder
            ->create()                                  // create a new JWS 
            ->withPayload($this->generatePayload($id, $sem, $firstname))
            ->addSignature($this->key, ['alg' => 'HS256'])   // add a signature with a simple protected header
            ->build(); 
        // once key has been used, store in db
        if ($this->db->removeExpiredKeys() && $this->db->storeKey($this->key, $id)) {
            return $this->serializer->serialize($jws, 0); // serialize the signature at index 0 (bc only have one signature)
        }
    }
    // get data from token 
    public function getPayloadFromToken($token) {
        $jws = $this->serializer->unserialize($token);
        return json_decode($jws->getPayload(), true); 
    }

    // verify that the token is valid
    public function verify($token) {
        $jwsVerifier = new JWSVerifier($this->algManager); 
        // attempt to load the token 
        $jws = $this->serializer->unserialize($token); 
        
        // validation 
        $headerCheckerManager = new HeaderCheckerManager(
            [
                new AlgorithmChecker(['HS256']), // We check the header "alg" (algorithm)
            ], 
            [
                new JWSTokenSupport(), // Adds JWS token type support
            ]
        );
        $claimCheckerManager = new ClaimCheckerManager(
            [
                new Checker\IssuedAtChecker(),
                new Checker\NotBeforeChecker(),
                new Checker\ExpirationTimeChecker()
            ]
        );
        $claims = json_decode($jws->getPayload(), true);
        try {
            // header checker will throw an exception if param is missing
            $headerCheckerManager->check($jws, 0, ['alg']);
            $claimCheckerManager->check($claims, ['iss', 'nbf', 'exp']);
        } catch (Exception $e) {
            echo $e->getMessage();
        } 

        // load in active keys
        $keyKs = $this->db->retrieveKeys(); 
        $keys = array(); 
        foreach ($keyKs as $keyK) {
            $jwk = new JWK([
                'kty' => 'oct',
                'k' => $keyK['key']
            ]);
            array_push($keys, $jwk);
        }
        $jwkset = new JWKSet($keys); 


        // verify signature - args: 
            // jws: the JWS object 
            // $jwkset: the set of currently active keys
            // 0: the index of the signature to check 
        // checks the currently active keys to see if there are any that match the given token
       return $jwsVerifier->verifyWithKeySet($jws, $jwkset, 0);
    }
}

?>
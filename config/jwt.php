<?php 
// necessary imports 
require_once '../vendor/autoload.php';
// algorithm 
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\Algorithm\HS256;
// building the jws 
use Jose\Component\Signature\JWSBuilder; 
use Jose\Component\KeyManagement\JWKFactory;
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
    // user info to store in token 
    private $id; 
    private $seminar; 
    private $serializer; 
    private $algManager; 
    private $key; 

    // constructor
    public function __construct($id, $sem){
        $this->id = $id; 
        $this->seminar = $sem; 
        $this->serializer = new CompactSerializer(); 
        $this->algManager = new AlgorithmManager([
            new HS256()
        ]);
        $this->key = $this->JWK(); 
    }

    // key (JWK) generator helper function
    private function JWK() {
        return JWKFactory::createOctKey(
            1024, // Size in bits of the key. We recommend at least 128 bits.
            [
                'alg' => 'HS256', // This key must only be used with the HS256 algorithm
                'use' => 'sig'    // This key is used for signature/verification operations only
            ]
        );
    }

    // payload generator helper function
    private function getPayload() {
        return json_encode([
            'iat' => time(), // issued at
            'nbf' => time(), // not before 
            'exp' => time() + 3600, // expiration
            'iss' => 'Montage', // issuer 
            'sem' => $this->seminar, // seminar
            'id' => $this->id // sid
        ]);
    }

    // create JWS 
    public function getJWS() {
        // instantiate JWS builder
        $jwsBuilder = new JWSBuilder($this->algManager);
        $jws = $jwsBuilder
            ->create()                                  // create a new JWS 
            ->withPayload($this->getPayload())
            ->addSignature($this->key, ['alg' => 'HS256'])   // add a signature with a simple protected header
            ->build(); 
        // serialize the jws obj
        return $this->serializer->serialize($jws, 0); // serialize the signature at index 0 (bc only have one signature)
    }

    // deserialize token 
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

        // verify signature - args: 
            // jws: the JWS object 
            // jwk: the key used to instantiate the token 
            // 0: the index of the signature to check 
        return $jwsVerifier->verifyWithKey($jws, $this->key, 0);
    }

    public function getId() {
        return $this->id; 
    }
}

?>
<?php

/**
 * Temboo PHP SDK core
 *
 * Execute Choreographies from the Temboo library.
 *
 * PHP version 5
 *
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   Services
 * @package    Temboo
 * @subpackage Core
 * @author     Temboo, Inc.
 * @copyright  2013 Temboo, Inc.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link       http://www.temboo.com
 */


/**
 * An authenticated Temboo session for executing choreographies.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_Session
{
    /**
     * Temboo account name for authentication.
     *
     * @var string
     */
    protected $accountName;

    /**
     * Temboo App Key name for authentication.
     *
     * @var string
     */
    protected $name;

    /**
     * Temboo App Key for authentication.
     *
     * @var string
     */
    protected $key;

    /**
     * Base domain for Temboo REST API.
     *
     * @var string
     */
    protected $domain = 'temboolive.com';

    /**
     * Base path for Temboo REST API.
     *
     * @var string
     */
    protected $basePath = '/arcturus-web/api-1.0/';

    /**
     * SDK client version.
     *
     * @var string
     */
    protected $clientVersion = 'PHPSDK_2.24.0';


    /**
     * An authenticated Temboo session for executing choreographies.
     *
     * You can add application keys for authentication by logging into https://temboo.com/ and visiting "My Account".
     *
     * @param string $accountName Temboo Account name.
     * @param string $name Temboo App Key name.
     * @param string $key Temboo App Key.
     * @return Temboo_Session New session.
     * @throws Temboo_Exception_Authentication if sufficient credentials are not provided.
     * @throws Temboo_Exception if libcurl or JSON support is not present.
     */
    public function __construct($accountName, $name, $key)
    {
        if(!extension_loaded('curl')) {
            throw new Temboo_Exception("Temboo_Session requires PHP libcurl extension.");
        }

        if(!extension_loaded('json')) {
            throw new Temboo_Exception("Temboo_Session requires PHP JSON extension.");
        }

        if(!$accountName) {
            throw new Temboo_Exception_Authentication("Missing required account name for API.");
        }
        $this->accountName = $accountName;

        if(!$name || !$key) {
            throw new Temboo_Exception_Authentication("Missing required credentials for API.");
        }
        $this->name = $name;
        $this->key = $key;
    }

    /**
     * Fully qualified uri for a given Temboo_URI fragment for this REST API connection.
     *
     * @param Temboo_URI $uri A URI fragment corresponding to a method call in the REST API.
     * @return string Complete uri, including protocol and host, for passing to http client library.
     */
    protected function fullRESTUri(Temboo_URI $uri)
    {
        $uriWithVersion = clone $uri;
        $uriWithVersion->setParameter('source_id', $this->getSourceId($uri));
        return 'https://' . $this->accountName . '.' . $this->domain . $this->basePath . $uriWithVersion;
    }

    /**
     * Builds the value for the 'source_id' parameter sent as part of REST API requests
     *
     * @param Temboo_URI A URI fragment corresponding to a method call in the REST API.
     * @return string The paramter value
     */  
    protected function getSourceId($uri)
    {
        $jsClientVersion = $uri->getJSClientVersion();
        return $this->clientVersion . ($jsClientVersion ? '-'.$jsClientVersion : '');
    }

    /**
     * HTTP GET request.
     *
     * @internal
     * @param Temboo_Resource $resource A temboo resource with a URI corresponding to a method call in the REST API.
     * @return Temboo_API_Response Response object encapsulating raw and formatted results and HTTP headers.
     * @throws Temboo_Exception_Authentication if session authentication fails.
     * @throws Temboo_Exception if HTTP request cannot be completed. Does NOT throw an exception for API-level errors.
     */
    public function get(Temboo_Resource $resource)
    {
        $uri = $resource->getUri();
        $curlHandle = $this->curlInit($uri);
        return $this->curlResponse($curlHandle, $uri, 'GET');
    }

    /**
     * HTTP PUT request.
     *
     * @internal
     * @param Temboo_Resource $resource A temboo resource with a URI corresponding to a method call in the REST API.
     * @param string $data Body of PUT request.
     * @return Temboo_API_Response Response object encapsulating raw and formatted results and HTTP headers.
     * @throws Temboo_Exception_Authentication if session authentication fails.
     * @throws Temboo_Exception if HTTP request cannot be completed. Does NOT throw an exception for API-level errors.
     */
    public function put(Temboo_Resource $resource, $data)
    {
        $uri = $resource->getUri();
        $curlHandle = $this->curlInit($uri);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
        return $this->curlResponse($curlHandle, $uri, 'PUT', $data);
    }

    /**
     * HTTP POST request.
     *
     * @internal
     * @param Temboo_Resource $resource A temboo resource with a URI corresponding to a method call in the REST API.
     * @param string $data Body of POST request.
     * @return Temboo_API_Response Response object encapsulating raw and formatted results and HTTP headers.
     * @throws Temboo_Exception_Authentication if session authentication fails.
     * @throws Temboo_Exception if HTTP request cannot be completed. Does NOT throw an exception for API-level errors.
     */
    public function post(Temboo_Resource $resource, $data)
    {
        $uri = $resource->getUri();
        $curlHandle = $this->curlInit($uri);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
        return $this->curlResponse($curlHandle, $uri, 'POST', $data);
    }

    /**
     * HTTP DELETE request.
     *
     * @internal
     * @param Temboo_Resource $resource A temboo resource with a URI corresponding to a method call in the REST API.
     * @return Temboo_API_Response Response object encapsulating raw and formatted results and HTTP headers.
     * @throws Temboo_Exception_Authentication if session authentication fails.
     * @throws Temboo_Exception if HTTP request cannot be completed. Does NOT throw an exception for API-level errors.
     */
    public function delete(Temboo_Resource $resource)
    {
        $uri = $resource->getUri();
        $curlHandle = $this->curlInit($uri);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "DELETE");
        return $this->curlResponse($curlHandle, $uri, 'DELETE');
    }

    /**
     * Initialize curl handle with options common to all request methods.
     *
     * @param Temboo_URI $uri A URI fragment corresponding to a method call in the REST API.
     * @return Resource Curl resource.
     * @throws Temboo_Exception if curl cannot be initialized or necessary HTTP parameters cannot be set.
     */
    private function curlInit($uri)
    {
        $curlHandle = curl_init($this->fullRESTUri($uri));
        if($curlHandle === false) {
            throw new Temboo_Exception('Curl initialization failed', array('uri' => $this->fullRESTUri($uri)));
        }

        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1) ;
        curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curlHandle, CURLOPT_USERPWD, $this->name . ":" . $this->key);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER,
            array(
                "x-temboo-domain: /" . $this->accountName . "/master",
                "Content-type: application/json",
                "Accept: application/json"
            )
        );

        return $curlHandle;
    }

    /**
     * Executes initialized curl connection for all HTTP methods and returns encapsulated Temboo_API_Response.
     *
     * @param Resource $curlHandle curl resource.
     * @param Temboo_URI $uri a URI fragment corresponding to a method call in the REST API.
     * @param string $method HTTP method that produced this response. One of GET, PUT, POST, DELETE.
     * @param string $data the data supplied for this response (optional, only present for PUT and POST requests).
     * @return Temboo_API_Response response object encapsulating raw and formatted results and HTTP headers.
     * @throws Temboo_Exception_Authentication if session authentication fails.
     * @throws Temboo_Exception if HTTP request cannot be completed. Does NOT throw an exception for API-level errors.
     */
    private function curlResponse($curlHandle, $uri, $method, $data = null)
    {
        $curlResponse = curl_exec($curlHandle);
        if($curlResponse === false) {
            if(curl_errno($curlHandle) == 6) { // CURLE_COULDNT_RESOLVE_HOST
                throw new Temboo_Exception_Authentication("Session authentication failed. User credentials were not supplied or the supplied credentials are invalid, and no current user session was found. Check your credentials and try again.");
            }
            throw new Temboo_Exception('Curl request failed.', array('uri' => $this->fullRESTUri($uri), 'curl_error' => curl_error($curlHandle)));
        }

        $curlInfo = curl_getinfo($curlHandle);

        curl_close($curlHandle);

        $response = new Temboo_API_Response($uri, $method, $curlInfo['http_code'], $curlInfo['content_type'], $curlResponse, $data);
        if($response->getStatus() == '401') {
            $response_array = $response->getArray();
            if($response_array && isset($response_array['error']) && isset($response_array['error']['message'])) {
                throw new Temboo_Exception_Authentication("Session authentication failed. " . $response_array['error']['message'], array('response' => $response));
            }
            throw new Temboo_Exception_Authentication("Session authentication failed.", array('response' => $response));
        }
        return $response;
    }
}


/**
 * A Choreography from the Temboo library.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_Choreography extends Temboo_Resource
{
    /**
     * A choreography from the Temboo library.
     *
     * @param Temboo_Session $session The session that owns this choreography.
     * @param Temboo_URI|string $uri A URI fragment corresponding to the method call in the REST API that accesses this choreography.
     * @return Temboo_Choregraphy New instance.
     * @throws Temboo_Exception if $uri is not valid.
     */
    public function __construct(Temboo_Session $session, $uri)
    {
        parent::__construct($session, 'choreos/' . $uri);
    }

    /**
     * Executes this choreography.
     *
     * @param Temboo_Inputs|array $inputs (optional) Inputs as Temboo_Inputs or associative array.
     * @param bool $async Whether to execute in asynchronous mode. Default false.
     * @param bool $store_results Whether to store results of asynchronous execution. Default true.
     * @return Temboo_Choreography_Execution execution resource.
     * @throws Temboo_Exception_Authentication if session authentication fails.
     * @throws Temboo_Exception_Execution if runtime errors occur in synchronous execution or execution fails to start. NOT thrown for post-launch errors in asynchronous execution -- check status or results to determine asynchronous success.
     * @throws Temboo_Exception_Notfound if choreography does not exist.
     * @throws Temboo_Exception if execution request fails.
     */
    public function execute($inputs = array(), $async = false, $store_results = true)
    {
        return new Temboo_Choreography_Execution($this->session, $this, $inputs, $async, $store_results);
    }

    /**
     * Obtains a new inputs object.
     *
     * @param array $inputs Associative array of input names and values.
     * @return Temboo_Inputs New inputs object.
     * @throws Temboo_Exception if provided input set is invalid. (Note an empty set is considered valid.)
     */
    public function newInputs($inputs = array())
    {
        return new Temboo_Inputs($inputs);
    }
    
}


/**
 * An executed/executing instance of a choreography.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_Choreography_Execution extends Temboo_Resource
{
    /**
     * Asynchronous mode?
     *
     * @var bool Whether to execute asynchronously. Default false.
     */
    private $_async = false;

    /**
     * Store asynchronous results?
     *
     * @var bool Whether to store results of asynchronous execution. Default false.
     */
    private $_store_results = true;

    /**
     * Choreography
     *
     * @var Temboo_Choreography Choreography that spawned this execution.
     */
    private $_choreo;

    /**
     * Execution id.
     *
     * @var string Execution id for asynchronous executions.
     */
    private $_id;

    /**
     * Execution status.
     *
     * @var string SUCCESS, ERROR, TERMINATED_MANUALLY, TERMINATED_LIMIT, RUNNING
     */
    private $_status;

    /**
     * Execution error.
     *
     * @var string Last error message, if any.
     */
    private $_error;

    /**
     * Execution result.
     *
     * @var Temboo_Results Output of execution.
     */
    private $_results;


    /**
     * An executed/executing instance of a choreography.
     *
     * @param Temboo_Session $session The session that owns this resource.
     * @param Temboo_Choreography $choreo The choregraphy object for the execution.
     * @param Temboo_Inputs|array $inputs (optional) Inputs as Temboo_Inputs or associative array.
     * @param bool $async Whether to execute in asynchronous mode. Default false.
     * @param bool $store_results Whether to store results of asynchronous execution. Default true.
     * @return Temboo_Choreography_Execution New execution.
     * @throws Temboo_Exception_Authentication if session authentication fails.
     * @throws Temboo_Exception_Execution if runtime errors occur in synchronous execution or execution fails to start. NOT thrown for post-launch errors in asynchronous execution -- check status or results to determine asynchronous success.
     * @throws Temboo_Exception_Notfound if choreography does not exist.
     */
    public function __construct(Temboo_Session $session, Temboo_Choreography $choreo, $inputs = array(), $async = false, $store_results = true)
    {
        parent::__construct($session, $choreo->getUri());
        
        if(!($inputs instanceof Temboo_Inputs)) {
            $inputs = $choreo->newInputs($inputs);
        }
        if($async) {
            $this->_async = true;
            $this->uri->setParameter('mode', 'async');
            if($store_results) {
                $this->uri->setParameter('store_results', 'true');
            } else {
                $this->_store_results = false;
            }

        }
        $response = $this->session->post($this, (string) $inputs);


        /*
         * Both synchronous asynchronous execution return a Temboo_Execution object.
         *
         * For sync, we execute immediately, throw exceptions on any runtime errors,
         * and otherwise store the response in the execution object immediately.
         *
         * For async, we launch the remote execution and get back an ID to check for
         * running status and (eventually) retrieve the results. We throw exceptions
         * on transport or authentication errors, but cannot tell if a runtime error
         * will occur -- instead, check $execution->getStatus() and
         * $execution->getError() to handle runtime errors without an exception.
         *
         * Calling $execution->getResults() on a failed (or still running) async execution
         * will throw an exception.
         *
         */
        if($this->_async) { // Error checking and set ID for asynchronous execution...
            if($response->getStatus() == '404') {
                throw new Temboo_Exception_Notfound('Choreography execution not found.', array('response' => $response));
            }
            $response_object = $response->getObject();
            if(!$response_object || !isset($response_object->id)) {
                if($response_object && isset($response_object->error) && isset($response_object->error->message)) {
                    $this->_error = $response_object->error->message;
                    throw new Temboo_Exception_Execution('Choreography execution failed. ' . $this->_error, array('response' => $response));
                }
                throw new Temboo_Exception_Execution('Choreography execution returned invalid result.', array('response' => $response));
            }
            if($response->error()) {
                throw new Temboo_Exception_Execution('Choreography execution failed.', array('response' => $response));
            }
            $this->_id = $response_object->id;
        } else { // Error checking and immediate packaging of response for synchronous execution...
            if($response->getStatus() == '404') {
                throw new Temboo_Exception_Notfound('Choreography not found.', array('response' => $response));
            }
            $response_array = $response->getArray();
            if(!$response_array || !isset($response_array['execution'])) {
                if($response_array && isset($response_array['error']) && isset($response_array['error']['message'])) {
                    $this->_error = $response_array['error']['message'];
                    throw new Temboo_Exception_Execution('Choreography execution failed. ' . $this->_error, array('response' => $response));
                }
               throw new Temboo_Exception_Execution('Choreography execution returned invalid result.', array('response' => $response));
            }
            if($response->error()) {
                if(isset($response_array['execution']['lasterror'])) {
                    $this->_error = $response_array['execution']['lasterror'];
                    throw new Temboo_Exception_Execution('Choreography execution failed. ' . $this->_error, array('response' => $response));
                }
                throw new Temboo_Exception_Execution('Choreography execution failed.', array('response' => $response));
            }
            if(isset($response_array['execution']['status'])) {
                $this->_status = $response_array['execution']['status'];
            }
            if(isset($response_array['execution']['lasterror'])) {
                $this->_error = $response_array['execution']['lasterror'];
            }
            if(isset($response_array['output'])) {
                $this->_results = $this->wrapResults($response_array['output']);
            }
        }
    }

    /**
     * Id for an asynchronous execution.
     *
     * @return string Execution id.
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Status of this choreography execution.
     *
     * @return string SUCCESS, ERROR, TERMINATED_MANUALLY, TERMINATED_LIMIT, RUNNING.
     * @throws Temboo_Exception_Authentication if session authentication fails.
     * @throws Temboo_Exception_Notfound if execution does not exist.
     * @throws Temboo_Exception if status request fails.
     */
    public function getStatus()
    {
        if($this->_async) {
            $executionStatus = new Temboo_Choreography_Status($this->session, $this->_id);
            $response = $this->session->get($executionStatus);
            if($response->getStatus() == '404') {
                throw new Temboo_Exception_Notfound('Choreography execution status not found.', array('response' => $response));
            }
            $response_object = $response->getObject();
            if(!$response_object || !isset($response_object->execution)) {
               throw new Temboo_Exception('Choreography execution status returned invalid result.', array('response' => $response));
            }
            $this->_status = $response_object->execution->status;
            if($this->_status == 'ERROR') {
                if(isset($response_object->error) && isset($response_object->error->message)) {
                    $this->_error = $response_object->error->message;
                } elseif(isset($response_object->execution->lasterror)) {
                    $this->_error = $response_object->execution->lasterror;
                }
            }
        }
        return $this->_status;
    }

    /**
     * Result of this choreography execution.
     *
     * @return Temboo_Results Named outputs of execution.
     * @throws Temboo_Exception_Authentication if session authentication fails.
     * @throws Temboo_Exception_Execution if runtime errors occured in asynchronous execution
     * @throws Temboo_Exception_Notfound if execution does not exist.
     * @throws Temboo_Exception if result request fails, or results requested for an asynchronous execution that has not finished.
     */
    public function getResults()
    {
        if($this->_async && $this->_store_results && !$this->_results) {
            $executionResult = new Temboo_Choreography_Result($this->session, $this->_id);
            $response = $this->session->get($executionResult);
            if($response->getStatus() == '404') {
                throw new Temboo_Exception_Notfound('Choreography execution result not found.', array('response' => $response));
            }
            $response_array = $response->getArray();
            if(!$response_array || !isset($response_array['execution'])) {
               throw new Temboo_Exception('Choreography execution result returned invalid result.', array('response' => $response));
            }
            if(isset($response_array['execution']['status']) && $response_array['execution']['status'] == 'ERROR') {
                if(isset($response_array['execution']['lasterror'])) {
                    $this->_error = $response_array['execution']['lasterror'];
                    throw new Temboo_Exception_Execution('Choreography execution failed. ' . $this->_error, array('response' => $response));
                }
                throw new Temboo_Exception_Execution('Choreography execution failed.', array('response' => $response));
            }
            if(isset($response_array['output'])) {
                $this->_results = $this->wrapResults($response_array['output']);
            }
        }
        if(!($this->_results instanceof Temboo_Results)) {
            throw new Temboo_Exception('No result available yet.', array('response' => $response));
        }
        return $this->_results;
    }

    /**
     * Last execution error.
     *
     * @return string Last error from execution, if any.
     */
    public function getError()
    {
        return $this->_error;
    }


    /**
     * Wrap results in appopriate results class for this choreography.
     *
     * @param array $outputs Associative array of output names and values.
     * @return Temboo_Results New result set.
     */
    protected function wrapResults($outputs)
    {
        return new Temboo_Results($outputs);
    }

}


/**
 * The status of an asynchronously executed choreography.
 *
 * @package Temboo
 * @subpackage Core
 * @internal
 */
class Temboo_Choreography_Status extends Temboo_Resource
{
    /**
     * The status of an asynchronously executed choreography.
     *
     * @param Temboo_Session $session The session that owns this choreography.
     * @param Temboo_URI|string $uri A URI fragment corresponding to the method call in the REST API that accesses this choreography.
     * @return Temboo_Choreography_Status New instance.
     * @throws Temboo_Exception if $uri is not valid.
     */
    public function __construct(Temboo_Session $session, $uri)
    {
        parent::__construct($session, 'choreo-executions/' . $uri);
    }
}


/**
 * The result of an asynchronously executed choreography.
 *
 * @package Temboo
 * @subpackage Core
 * @internal
 */
class Temboo_Choreography_Result extends Temboo_Resource
{
    /**
     * The result of an asynchronously executed choreography.
     *
     * @param Temboo_Session $session The session that owns this choreography.
     * @param Temboo_URI|string $uri A URI fragment corresponding to the method call in the REST API that accesses this choreography.
     * @return Temboo_Choreography_Result New Instance.
     * @throws Temboo_Exception if $uri is not valid.
     */
    public function __construct(Temboo_Session $session, $uri)
    {
        parent::__construct($session, 'choreo-executions/' . $uri);
        $this->uri->setParameter('view', 'outputs');
    }
}


/**
 * Parent class for all RESTful resources associated with a Temboo_Session.
 *
 * @package Temboo
 * @subpackage Core
 */
abstract class Temboo_Resource
{
    /**
     * The session that owns this resource.
     *
     * @var Temboo_Session
     */
    protected $session;

    /**
     * A URI fragment corresponding to the method call in the REST API that accesses this resource.
     *
     * @var Temboo_URI
     */
    protected $uri;

    /**
     * A RESTful resources associated with a Temboo_Session.
     *
     * @param Temboo_Session $session The session that owns this resource.
     * @param Temboo_URI|string $uri A URI fragment corresponding to the method call in the REST API that accesses this resource.
     * @return Temboo_Resource New instance.
     * @throws Temboo_Exception if $uri is not valid.
     */
    protected function __construct(Temboo_Session $session, $uri)
    {
        $this->session = $session;
        if(!($uri instanceof Temboo_URI)) {
            $uri = new Temboo_URI($uri);
        }
        $this->uri = $uri;
    }

    /**
     * The session that owns this resource.
     *
     * @return Temboo_Session The session that owns this resource.
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * The URI for this resource.
     *
     * @return Temboo_URI A URI fragment corresponding to the method call in the REST API that accesses this resource.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set the JavaScript client version for proxied requests
     *
     * @return void
     */
    public function setJSClientVersion($version)
    {
        $this->uri->setJSClientVersion($version);
    }

    /**
     * The URI string for this resource.
     *
     * @return string URI for this resource.
     */
    public function __toString()
    {
        return $this->uri->__toString();
    }
}


/**
 * Encapsulates inputs and formats them for posting.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_Inputs
{
    /**
     * Associative array of input names and values
     *
     * @var array
     */
    protected $inputArray = array();

    /**
     * Indexed array of output filters
     * @var array
     */
    protected $outputFilters = array();

    /**
     * Credential preset
     *
     * @var string
     */
    protected $credential;


    /**
     * Encapsulates inputs and formats them for posting.
     *
     * @param array $inputs Associative array of input names and values.
     * @return Temboo_Inputs New inputs.
     * @throws Temboo_Exception if provided input set is invalid. (Note an empty set is considered valid.)
     */
    public function __construct($inputs = array())
    {
        if(!isset($inputs)) {
            $inputs = array();
        } elseif(!is_array($inputs)) {
            throw new Temboo_Exception('Invalid input set.', $inputs);
        }
        $this->inputArray = $inputs;
    }

    /**
     * Set arbitrary input
     *
     * @param string $name Input name.
     * @param string $value Input value.
     * @return Temboo_Inputs For method chaining.
     */
    public function set($name, $value)
    {
        $this->inputArray[$name] = $value;
        return $this;
    }

    /**
     * Set credential
     *
     * @param string $credentialName The name of a Credential in your account specifying presets for this set of inputs.
     * @return Temboo_Inputs For method chaining.
     */
    public function setCredential($credentialName)
    {
        $this->credential = $credentialName;
        return $this;
    }

    /**
     * Add profile -- alias of setCredential
     *
     * @param string $profileName The name of a Profile in your account specifying presets for this set of inputs.
     * @return Temboo_Inputs For method chaining.
     */
    public function addProfile($profileName)
    {
        return $this->setCredential($profileName);
    }

    /**
     * Add output filter
     * @param string $filterName Output filter name.
     * @param string $path Xpath for output filter.
     * @param string $outputVariableSource The output to filter.
     */
     public function addOutputFilter($filterName, $path, $outputVariableSource)
     {
        $this->outputFilters[] = array('name' => $filterName, 'path' => $path, 'variable' => $outputVariableSource);
     }

    /**
     * Format input array as JSON suitable for passing to REST API.
     *
     * @return string JSON choreography execution input string.
     */
    public function __toString()
    {
        $fullInputs = array('inputs' => array());
        foreach($this->inputArray as $name => $value) {
            $fullInputs['inputs'][] = array('name' => $name, 'value' => $value);
        }
        if($this->credential) {
            $fullInputs['preset'] = $this->credential;
        }
        if(count($this->outputFilters) > 0) {
            $fullInputs['outputFilters'] = $this->outputFilters;
        }
        return json_encode($fullInputs);
    }
}


/**
 * Encapsulates execution output.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_Results
{
    /**
     * Associative array of outputs
     *
     * @var array
     */
    protected $outputArray = array();

    /**
     * Map lowercase keys to keys as they were returned from REST API, for case insensitive lookup
     *
     * @var array
     */
    protected $lowercaseKeyMap = array();

    /**
     * Encapsulates execution output.
     *
     * @param array $outputs Associative array of output names and values.
     * @return Temboo_Results New instance.
     * @throws Temboo_Exception if provided output set is invalid. (Note an empty set is considered valid.)
     */
    public function __construct($outputs = array())
    {
        if(!is_array($outputs)) {
            throw new Temboo_Exception('Invalid result set.', $outputs);
        }
        $this->outputArray = $outputs;
        foreach(array_keys($outputs) as $key) {
            $lowerKey = strtolower($key);
            $this->lowercaseKeyMap[$lowerKey] = $key;
        }
    }

    /**
     * Get arbitrary output by name.
     *
     * @param string $name (optional) Output name. Case insensitive.
     * @return mixed|array Value of named output, or associative array of all outputs if name is omitted.
     * @throws Temboo_Exception_Notfound if output does not exist. (Note an empty response is considered valid.).
     */
    public function get($name = null)
    {
        if($name) {
            if(isset($this->outputArray[$name])) {
                return $this->outputArray[$name];
            } else {
                $lowerName = strtolower($name);
                if(isset($this->lowercaseKeyMap[$lowerName])) {
                    $originalName = $this->lowercaseKeyMap[$lowerName];
                    if(isset($this->outputArray[$originalName])) {
                        return $this->outputArray[$originalName];
                    }
                }
            }
            throw new Temboo_Exception('Output not found.', $name);
        } else {
            return $this->outputArray;
        }
    }

    /**
     * Get arbitrary output by name as string.
     *
     * @param string $name Output name. Case insensitive.
     * @return string Value of named output.
     * @throws Temboo_Exception_Notfound if output does not exist. (Note an empty response is considered valid.).
     */
    public function getResultString($name)
    {
        $output = $this->get($name);
        return is_array($output) ? json_encode($output) : $output;
    }

    /**
     * Get arbitrary output by name as array.
     *
     * @param string $name Output name. Case insensitive.
     * @return array Value of named output.
     * @throws Temboo_Exception_Notfound if output does not exist. (Note an empty response is considered valid.).
     */
    public function getResultList($name)
    {
        $output = $this->get($name);
        return is_array($output) ? $output : array($output);
    }

    /**
     * Utility function, to retrieve the scalar sub-item specified by the key from the parent (array) specified by the item.
     * Returns null if key is not present.
     */
    public static function getSubItemByKey($item, $key) {
        if(!is_array($item))
            $item = json_decode($item, true);

        return $item[$key];
    }

    /**
     * Utility function, to retrieve the array-type sub-item specified by the key from the parent (array) specified by the item.
     * Returns an empty array if key is not present.
     */
    public static function getSubArrayByKey($item, $key) {
        if(!is_array($item))
            $item = json_decode($item, true);

        $val = $item[$key];
        if(!is_null($val)) {
            return $val;
        } else {
            return array();
        }
    }
}


/**
 * Encapsulates REST API responses, providing access to raw and formatted results and HTTP headers.
 *
 * Used internally for Temboo API requests, and provided for debugging in applicable exceptions.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_API_Response
{
    /**
     * A URI fragment corresponding to the method call in the REST API that produced this response.
     *
     * @var Temboo_URI
     */
    private $_uri;

    /**
     * The HTTP method that produced this response. One of GET, PUT, POST, DELETE.
     *
     * @var string
     */
    private $_method;


    /**
     * The HTTP status code for this response. E.g. 200 (OK), 404 (not found).
     *
     * @var int
     */
    private $_status;

    /**
     * The MIME content type for this response. E.g. applicatin/json, application/xml.
     *
     * @var string
     */
    private $_contentType;

    /**
     * The raw body of this response.
     *
     * @var string
     */
    private $_response;

    /**
     * The data supplied for this response (optional, only present for PUT and POST requests).
     *
     * @var string
     */
    private $_data;

    /**
     * Convenience variable for whether the request produced an HTTP status indicating an error condition (400s and 500s).
     *
     * @var bool
     */
    private $_error = false;

    /**
     * Encapsulates REST API responses, providing access to raw and formatted results and HTTP headers.
     *
     * Used internally for Temboo API requests, and provided for debugging in applicable exceptions.
     *
     * @param Temboo_URI $uri A URI fragment corresponding to the method call in the REST API that produced this response.
     * @param string $method The HTTP method that produced this response. One of GET, PUT, POST, DELETE.
     * @param string $status The HTTP status code for this response. E.g. 200 (OK), 404 (not found).
     * @param string $contentType The MIME content type for this response. E.g. applicatin/json, application/xml.
     * @param string $response The raw body of this response.
     * @param string $data The data supplied for this response (optional, only present for PUT and POST requests).
     * @return Temboo_API_Response New instance.
     */
    public function __construct(Temboo_URI $uri, $method, $status, $contentType, $response, $data = null)
    {
        $this->_uri = $uri;
        $this->_method = $method;
        $this->_status = $status;
        $this->_contentType = $contentType;
        $this->_response = $response;
        $this->_data = $data;
        if($status < 200 || $status >= 400) {
            $this->_error = true;
        }
    }

    /**
     * URI for this response.
     *
     * @return Temboo_URI A URI fragment corresponding to the method call in the REST API that produced this response.
     */
    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * HTTP method for this response..
     *
     * @return string The HTTP method that produced this response. One of GET, PUT, POST, DELETE.
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * HTTP status code for this response.
     *
     * @return int The HTTP status code for this response. E.g. 200 (OK), 404 (not found).
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * MIME type for this response.
     *
     * @return string The MIME content type for this response. E.g. applicatin/json, application/xml.
     */
    public function getContentType()
    {
        return $this->_contentType;
    }

    /**
     * Data for this response.
     *
     * @return string The data supplied for this response (optional, only present for PUT and POST requests).
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Raw response body.
     *
     * @return string The raw body of this response.
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Magic __toString method returns raw response.
     *
     * @return string The raw body of this response.
     */
    public function __toString()
    {
        return $this->getResponse();
    }

    /**
     * Convenience method for whether the request produced an HTTP status indicating an error condition (400s and 500s).
     *
     * @return bool True if status code indicated an error in request processing, false otherwise.
     */
    public function error()
    {
        return $this->_error;
    }

    /**
     * Formatted response as PHP standard object from JSON requests.
     *
     * @return stdClass Decoded response.
     * @throws Temboo_Exception if the response could not be decoded.
     */
    public function getObject()
    {
        // Special case for empty strings.
        if(strlen($this->_response) === 0) {
            return '';
        }
        $objectFromJson = json_decode($this->_response);
        if(!$objectFromJson) {
            throw new Temboo_Exception("API returned invalid JSON response.", array('response' => $this->_response));
        }
        return $objectFromJson;
    }

    /**
     * Formatted response as nested array from JSON requests.
     *
     * @return array Decoded response.
     * @throws Temboo_Exception if the response could not be decoded.
     */
    public function getArray()
    {
        // Special case for empty strings.
        if(strlen($this->_response) === 0) {
            return array();
        }
        $arrayFromJson = json_decode($this->_response, true);
        if(!$arrayFromJson) {
            throw new Temboo_Exception("API returned invalid JSON response.", array('response' => $this->_response));
        }
        return $arrayFromJson;
    }
}


/**
 * Encapsulates and normalizes a URI fragment for a Temboo resource.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_URI
{
    /**
     * The JavaScript client version, used when proxying requests
     *
     * @var String
     */
    protected $jsClientVersion;

    /**
     * An array of URI segments.
     *
     * @var array
     */
    private $_pathArray = array();

    /**
     * A named array of query string parameters.
     *
     * @var array
     */
    private $_parameter = array();

    /**
     * Encapsulates and normalizes a URI fragment for a Temboo resource.
     *
     * @param string|array $uri A URI fragment or ordered array of URI segments.
     * @param string|array $parameters A query string or named array of query string parameters.
     * @return Temboo_URI New URI.
     * @throws Temboo_Exception if $uri is not valid.
     */
    public function __construct($uri, $parameters = array())
    {
        if(is_array($uri)) {
            $this->_pathArray = $uri;
        } else {
            $this->_pathArray = preg_split('@/@', $uri, null, PREG_SPLIT_NO_EMPTY);
        }

        foreach($this->_pathArray as $pathSegment) {
            $this->validateSegment($pathSegment);
        }

        if(is_array($parameters)) {
            $this->_parameters = $parameters;
        } else {
            parse_str(preg_replace('/^\?/', '', $parameters), $this->_parameters);
        }

    }

    /**
     * Get the JavaScript client version for proxied reqeusts
     *
     * @return String the JavaScript client version calling this choreo, or null if not a proxied request
     */
    public function getJSClientVersion()
    {
        return $this->jsClientVersion;
    }

    /**
     * Set the JavaScript client version for proxied reqeusts
     *
     * @return void
     */
    public function setJSClientVersion($version)
    {
        $this->jsClientVersion = $version;
    }

    /**
     * Add/update parameters for URI.
     *
     * @param string $key The name of the parameter.
     * @param string $value (optional) Value of parameter. Leave empty to unset a previously set parameter.
     */
    public function setParameter($key, $value = null)
    {
        if(isset($value)) {
            $this->_parameters[$key] = $value;
        } else {
            unset($this->_parameters[$key]);
        }
    }

    /**
     * Normalized URI with proper slashes and query parameters.
     *
     * @return string Normalized URI.
     */
    public function __toString()
    {
        if(count($this->_parameters) > 0) {
            $encoded_parameters = array();
            foreach($this->_parameters as $key => $value) {
                $encoded_parameters[] = rawurlencode($key) . "=" . rawurlencode($value);
            }
            return implode('/', $this->_pathArray) . '?' . implode('&', $encoded_parameters);
        }
        return implode('/', $this->_pathArray);
    }

    /**
     * Validate URI segment.
     *
     * @param string $pathSegment An individual segment of a URI path.
     * @throws Temboo_Exception if $pathSegment is not a valid.
     */
    protected function validateSegment($pathSegment)
    {
        if(!preg_match("/^\w[\w\-\.@]*$/", $pathSegment)) {
            throw new Temboo_Exception("Invalid URI.");
        }
    }
}


/**
 * Proxies execution requests from the Temboo Javascript Library.
 *
 * Allowed choreographies and javascript-writeable inputs must be whitelisted.
 * Authentication to Temboo is internal to the proxy object, via a standard
 * Temboo_Session. Authenticating the JS library client request to the PHP
 * process containing the proxy is left up to the PHP application.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_Proxy
{
    /**
     * A named array of whitelisted choreography/input/allowed input combinations.
     *
     * @var Temboo_Proxied_Choreography[]
     */
    protected $_choreos = array();

    /**
     * Proxies execution requests from the Temboo Javascript Library.
     *
     * An initial whitelisted choreography can optionally be passed to the constructor.
     * Additional items can be added with Temboo_Proxy::addChoreo().
     *
     * @param string $name (optional) An arbitrary name, by which the JS library will reference the first choreography/input set.
     * @param Temboo_Choreography|Temboo_Proxied_Choreography $choreo (optional) An initial whitelisted choreography, or a complete whitelist item with choreography inputs.
     * @param Temboo_Inputs|array $inputs (optional) Default/fixed inputs as Temboo_Inputs or associative array.
     * @param string $allowedUserInput,... (optional) One or more inputs the JS request can (over)write.
     * @return Temboo_Proxy New Temboo_Proxy.
     */
    public function __construct($name = null, $choreo = null, $inputs = null, $allowedUserInput = null)
    {
        if(func_num_args() > 0) {
            call_user_func_array(array($this, 'addChoreo'), func_get_args());
        }
    }

    /**
     * Whitelist a choregraphy and associated inputs for use with this Temboo_Proxy.
     *
     * Each proxied object is a combination of Temboo_Choregraphy,
     * Temboo_Inputs, and a list of inputs the JS request can (over)write.
     * A single Temboo_Choregraphy can thus be added to the proxy multiple
     * times with different input configurations.
     *
     * @param string $name An arbitrary name, by which the JS library references this particular choreography/input set.
     * @param Temboo_Choreography|Temboo_Proxied_Choreography $choreo The choreography, or a complete whitelist item with choreography inputs.
     * @param Temboo_Inputs|array $inputs (optional) Default/fixed inputs as Temboo_Inputs or associative array.
     * @param string|array $allowedUserInput,... (optional) One or more inputs the JS request can (over)write.
     * @return Temboo_Proxy For method chaining.
     */
    public function addChoreo($name, $choreo, $inputs = null, $allowedUserInput = null)
    {
        if(!($choreo instanceof Temboo_Proxied_Choreography)) {
            $choreo = new Temboo_Proxied_Choreography($choreo);
        }
        if($inputs) {
            $choreo->setInputs($inputs);
        }
        foreach(array_slice((func_get_args()), 3) as $allowedUserInput) {
            if(is_array($allowedUserInput)) {
                $choreo->allowUserInputs($allowedUserInput);
            } else {
                $choreo->allowUserInput($allowedUserInput);
            }
        }
        $this->_choreos[$name] = $choreo;
        return $this;
    }

    /**
     * Attempt to execute proxied choreography as requested from JS library and return formatted response.
     *
     * Never throws an exception -- all errors are instead returned to the JS library for handling.
     *
     * @param string|stdClass|array $request The contents of the POST parameter from the JS library, as either a JSON string or already decoded.
     * @param bool $jsonEncode (optional) Whether to JSON-encode output. Default true. If set to false, containing script must encode output before returning it to the JS library.
     * @return string|stdClass JSON string or unencoded object for return to JS library.
     */
    public function execute($request, $jsonEncode = true)
    {
        try {
            if(is_string($request)) {
                $request = json_decode($request);
            } elseif(is_array($request)) {
                $request = json_decode(json_encode($request)); // Normalize array decoding to object decoding.
            }
            if(!isset($request->name) || !isset($this->_choreos[$request->name])) {
                throw new Temboo_Exception_Notfound('Proxied Choreo not found.', array('name' => $name));
            }
            if(!isset($request->version)) {
                throw new Temboo_Exception('Missing required "version" input');
            }
            $inputs = isset($request->inputs) ? $request->inputs : array();
            $outputFilters = isset($request->outputFilters) ? $request->outputFilters : array();
            $results = $this->_choreos[$request->name]->execute($inputs, $outputFilters, $request->version)->getResults();
            $response = array('success' => true, 'outputs' => $results->get());
        }
        /**
         * Catch exceptions and normalize error types for JS library.
         * JS error types for Temboo-specific exceptions: DisallowedInput, NotFound, Execution, Authentication, Temboo
         * Non-temboo exceptions should return type Server, and include an additional nativeType with the exception class from the server-side language.
         * Note that we do not pass on the original message for Execution errors, as they may contain sensitive information.
         */
        catch(Temboo_Exception_DisallowedInput $e) {
            $response = array('error' => true, 'type' => 'DisallowedInput', 'message' => $e->getMessage(), 'inputName' => $e->getInputName());
        } catch(Temboo_Exception_Notfound $e) {
            $response = array('error' => true, 'type' => 'NotFound', 'message' => $e->getMessage());
        } catch(Temboo_Exception_Execution $e) {
            $response = array('error' => true, 'type' => 'Execution', 'message' => 'An error occurred during choreo execution. Check your Temboo logs for details.');
        } catch(Temboo_Exception_Authentication $e) {
            $response = array('error' => true, 'type' => 'Authentication', 'message' => $e->getMessage());
        } catch(Temboo_Exception $e) {
            $response = array('error' => true, 'type' => 'Temboo', 'message' => $e->getMessage());
        } catch(Exception $e) {
            $response = array('error' => true, 'type' => 'Server', 'nativeType' => get_class($e), 'message' => $e->getMessage());
        }
        $response = json_encode($response);
        return $jsonEncode ? $response : json_decode($response); // Normalize array decoding to object decoding.
    }

    /**
     * Set default/fixed inputs for a proxied choreography.
     *
     * @param string $name An arbitrary name, by which the JS library references this particular choreography/input set.
     * @param Temboo_Inputs|array $inputs (optional) Default/fixed inputs as Temboo_Inputs or associative array.
     * @return Temboo_Proxy For method chaining.
     * @throws Temboo_Exception_Notfound if proxied choreography does not exist.
     */
    public function setDefaultInputs($name, $inputs)
    {
        if(!isset($this->_choreos[$name])) {
            throw new Temboo_Exception_Notfound('Proxied Choreo not found.', array('name' => $name));
        }
        $this->_choreos[$name]->setInputs($inputs);
        return $this;
    }

    /**
     * Allow JS to (over)write input for a proxied choreography.
     *
     * @param string $name An arbitrary name, by which the JS library references this particular choreography/input set.
     * @param string $input The name of an input JS is allowed to set.
     * @return Temboo_Proxy For method chaining.
     * @throws Temboo_Exception_Notfound if proxied choreography does not exist.
     */
    public function allowUserInput($name, $input)
    {
        if(!isset($this->_choreos[$name])) {
            throw new Temboo_Exception_Notfound('Proxied Choreo not found.', array('name' => $name));
        }
        $this->_choreos[$name]->allowUserInput($input);
        return $this;
    }

    /**
     * Allow JS to (over)write input for a proxied choreography.
     *
     * @param string $name An arbitrary name, by which the JS library references this particular choreography/input set.
     * @param array|string $inputs The name of one or more inputs JS is allowed to set.
     * @return Temboo_Proxy For method chaining.
     * @throws Temboo_Exception_Notfound if proxied choreography does not exist.
     */
    public function allowUserInputs($name, $inputs)
    {
        if(!isset($this->_choreos[$name])) {
            throw new Temboo_Exception_Notfound('Proxied Choreo not found.', array('name' => $name));
        }
        $this->_choreos[$name]->allowUserInputs($inputs);
        return $this;
    }
}


/**
 * Whitelisted choregraphy and associated inputs for use with a Temboo_Proxy.
 *
 * Each proxied object is a combination of Temboo_Choregraphy,
 * Temboo_Inputs, and a list of inputs the JS request can (over)write.
 * A single Temboo_Choregraphy can thus be added to the proxy multiple
 * times with different input configurations.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_Proxied_Choreography
{

    /**
     * The choreography.
     *
     * @var Temboo_Choreography
     */
    protected $_choreo;

    /**
     * Default/fixed inputs.
     *
     * @var Temboo_Inputs
     */
    protected $_inputs;

    /**
     * A list of inputs the JS request can (over)write.
     *
     * @var array
     */
    protected $_allowedUserInputs = array();

    /**
     * Whitelisted choregraphy and associated inputs for use with a Temboo_Proxy.
     *
     * @param Temboo_Choreography $choreo The choreography.
     * @param Temboo_Inputs|array $inputs (optional) Default/fixed inputs as Temboo_Inputs or associative array.
     * @param string $allowedUserInputs,... (optional) One or more inputs the JS request can (over)write.
     * @return Temboo_Proxied_Choreography New Temboo_Proxied_Choreography.
     */
    public function __construct(Temboo_Choreography $choreo, $inputs = null, $allowedUserInput = null)
    {
        $this->_choreo = $choreo;
        $this->setInputs($inputs);
        foreach(array_slice((func_get_args()), 3) as $allowedUserInput) {
            $this->allowUserInput($allowedUserInput);
        }
    }

    /**
     * Set default/fixed inputs for this proxied choreography.
     *
     * @param Temboo_Inputs|array $inputs (optional) Default/fixed inputs as Temboo_Inputs or associative array.
     * @return Temboo_Proxied_Choreography For method chaining.
     */
    public function setInputs($inputs)
    {
        if(!($inputs instanceof Temboo_Inputs)) {
            $inputs = $this->_choreo->newInputs($inputs);
        }
        $this->_inputs = $inputs;
        return $this;
    }

    /**
     * Allow JS to (over)write input.
     *
     * @param string $input The name of an input JS is allowed to set.
     * @return Temboo_Proxied_Choreography For method chaining.
     */
    public function allowUserInput($input)
    {
        if(!in_array($input, $this->_allowedUserInputs)) {
            $this->_allowedUserInputs[] = $input;
        }
        return $this;
    }

    /**
     * Allow JS to (over)write inputs.
     *
     * @param array|string $inputs The name of one or more inputs JS is allowed to set.
     * @return Temboo_Proxied_Choreography For method chaining.
     */
    public function allowUserInputs($inputs)
    {
        is_string($inputs) && ($inputs = array($inputs));
        foreach($inputs as $input) {
            $this->allowUserInput($input);
        }
        return $this;
    }

    /**
     * Execute choreography with defined/allowed inputs.
     *
     * @param array|stdClass $userInputs Inputs supplied by JS library request.
     * @param array|stdClass $outputFilters Output filters supplied by JS library request.
     * @return Temboo_Choreography_Execution execution resource.
     * @throws Temboo_Exception_DisallowedInput if JS-supplied input is not allowed.
     * @throws Temboo_Exception_Authentication if session authentication fails.
     * @throws Temboo_Exception_Execution if runtime errors occur in synchronous execution or execution fails to start.
     * @throws Temboo_Exception_Notfound if choreography does not exist.
     * @throws Temboo_Exception if execution request fails.
     */
    public function execute($userInputs = array(), $outputFilters = array(), $clientVersion) {
        $fullInputs = clone $this->_inputs;
        if(is_object($userInputs)) {
            $userInputs = get_object_vars($userInputs); // For JSON object deoding.
        }
        foreach($userInputs as $key => $value) {
            if(!in_array($key, $this->_allowedUserInputs)) {
                throw new Temboo_Exception_DisallowedInput('Input not allowed.', $key);
            }
            $fullInputs->set($key, $value);
        }
        foreach($outputFilters as $name => $props){
            $fullInputs->addOutputFilter($name, $props->path, $props->variable);
        }
        
        $this->_choreo->setJSClientVersion($clientVersion);

        return $this->_choreo->execute($fullInputs);
    }
}


/**
 * Temboo exception for general errors/catchall.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_Exception extends Exception
{
    /**
     * Additional debug info for the error.
     *
     * @var array
     */
    protected $_details;

    /**
     * Temboo exception for general errors/catchall.
     *
     * @param string $message Error message.
     * @param array $details (optional) Additional debug info, e.g. Temboo_API_Response from requests that raised an exception.
     * @return Temboo_Exception New exception.
     */
    public function __construct($message, $details = array())
    {
        parent::__construct($message);
        $this->_details = $details;
    }

    /**
     * Additional debug info, e.g. Temboo_API_Response from requests that raised an exception.
     *
     * @return array array with extra context and information about the error.
     */
    public function getDetails()
    {
        return $this->_details;
    }
}


/**
 * Temboo exception for authentication errors.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_Exception_Authentication extends Temboo_Exception
{
    /**
     * Temboo exception for authentication errors.
     *
     * E.g. invalid session credentials.
     *
     * @param string $message Error message.
     * @param array $details (optional) Additional debug info, e.g. Temboo_API_Response from requests that raised an exception.
     * @return Temboo_Exception_Authentication New exception.
     */
    public function __construct($message, $details = array())
    {
        parent::__construct($message, $details);
    }
}


/**
 * Temboo exception for missing/unknown resources.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_Exception_Notfound extends Temboo_Exception
{
    /**
     * Temboo exception for missing/unknown errors.
     *
     * E.g. attempting to access a deleted or renamed choreography.
     *
     * @param string $message Error message.
     * @param array $details (optional) Additional debug info, e.g. Temboo_API_Response from requests that raised an exception.
     * @return Temboo_Exception_Notfoud New exception.
     */
    public function __construct($message, $details = array())
    {
        parent::__construct($message, $details);
    }
}


/**
 * Temboo exception for errors during execution.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_Exception_Execution extends Temboo_Exception
{
    /**
     * Temboo exception for choreography execution errors.
     *
     * E.g. a runtime error in the choreography itself, such as a broken 3rd party API.
     *
     * @param string $message Error message.
     * @param array $details (optional) Additional debug info, e.g. Temboo_API_Response from requests that raised an exception.
     * @return Temboo_Exception_Execution New exception.
     */
    public function __construct($message, $details = array())
    {
        parent::__construct($message, $details);
    }
}


/**
 * Temboo exception for disallowed proxy inputs from JS library.
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_Exception_DisallowedInput extends Temboo_Exception
{

    /**
     * Name of the disallowed input.
     *
     * @var string
     */
    protected $_inputName;

    /**
     * Temboo exception for disallowed proxy inputs from JS library.
     *
     * @param string $message Error message.
     * @param string $inputName Name of the disallowed input.
     * @param array $details (optional) Additional debug info, e.g. Temboo_API_Response from requests that raised an exception.
     * @return Temboo_Exception_Execution New exception.
     */
    public function __construct($message, $inputName, $details = array())
    {
        parent::__construct($message, $details);
        $this->_inputName = $inputName;
    }

    /**
     * Name of the disallowed input.
     *
     * @return string Name of the disallowed input.
     */
    public function getInputName()
    {
        return $this->_inputName;
    }
}


/**
 * Autoloader for Temboo Library
 *
 * @package Temboo
 * @subpackage Core
 */
class Temboo_Loader
{
    /**
     * Autoloader for Temboo Library. Used with spl_autoload_register().
     *
     * @param string $class Class name to attempt to load.
     * @return bool True if matching file was found.
     */
    public static function autoload($class)
    {
        if(strpos($class, '_') !== false) {
            $path = preg_split('/_/', strtolower(trim($class, '_')));
            if($path[0]) {
                $filename = 'temboo.' . $path[0] . '.php';
            }
        } elseif($class == 'MyTemboo') {
            $filename = 'mytemboo.php';
        }
        if(isset($filename)) {
            $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . $filename;
            if(file_exists($file)) {
                require_once($file);
                return true;
            }
        }
        return false;
    }
}

spl_autoload_register(array('Temboo_Loader', 'autoload'));

?>

<?php
/**
 * Joy Web Framework
 *
 * Copyright (c) 2008-2009 Netology Foundation (http://www.netology.org)
 * All rights reserved.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL.
 */

/**
 * @package     Joy
 * @author      Hasan Ozgan <meddah@netology.org>
 * @copyright   2008-2009 Netology Foundation (http://www.netology.org)
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version     $Id$
 * @link        http://joy.netology.org
 * @since       0.5
 */
class Joy_Router extends Joy_Object
{
    /**
     * var array $_items
     */
    private $_items;

    /**
     * var object $_instance
     */
    private static $_instance;

    /**
     * getInstance
     * 
     * @return void
     */
    public function getInstance()
    {
        if (!is_object(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        parent::__construct(); 
        $routers = new Joy_Config_Section($this->config->application->get("files/config/router"));
        $items = $routers->getAll();

        foreach ($items as $key=>$item) {
            $this->_items[] = new Joy_Router_Item($item["url"], $item["controller"], $item["action"]);
        }
    }

    public function addItem(Joy_Router_Item $item)
    {
        $this->_items[] = $item;
    }

    public function resetItems()
    {
        $this->_items = array();
    }

    public function match($uri)
    {
        list($uri) = explode("&", $uri);
        list($uri) = explode("?", $uri);

        $atoms = (array) explode("/", $uri);
        $uri = "";
        foreach ($atoms as $atom) {
           if (empty($atom)) continue;
           $uri .= "/$atom";
        }

        $uri = sprintf("%s", trim($uri, DIRECTORY_SEPARATOR));

        $site_root = trim($this->config->application->get("application/site_root"), DIRECTORY_SEPARATOR);
        if (!empty($site_root)) {
            $uri = str_replace($site_root, "", $uri);
        }
        
        foreach ($this->_items as $key=>$item) {
            // check uri
            if (preg_match("/^{$item->filter}/i", "/{$uri}/", $matches)) {
                $matched_uri = str_replace($matches[0], "", $uri);
                array_shift($matches);

                // match uri
                $action_arguments = array();
                if ($matched_uri != "") {
                    $action_arguments = explode("/", trim($matched_uri, DIRECTORY_SEPARATOR));
                }

                // merge filter variables
                $parameters = array();
                foreach($item->variables as $key) {
                    if (!empty($key)) {
                        $parameters[$key] = trim(array_shift($matches), DIRECTORY_SEPARATOR);
                    }
                }
 
                // set controller variable
                if (!isset($item->controller)) {
                    $item->controller = $parameters["controller"];
                    unset($parameters["controller"]);
                }

                // set action variable
                if (!isset($item->action)) {
                    $item->action = $parameters["action"];
                    unset($parameters["action"]);
                }

                // set action arguments
                $item->action_arguments = array_merge(array_values((array)$parameters), (array)$action_arguments);

                // clear action from extension
                $action_info = explode(".", $item->action);
                 
                // set extension variable
                if (!isset($item->action_extension)) {
                    array_shift($action_info);
                    $item->action_extension = count($action_info) 
                                                  ? implode(".", $action_info)
                                                  : null;
                }

                //set parameters
                $item->parameters = array_merge((array)$parameters, $_REQUEST);

                // unset filter values
                unset($item->filter);
                unset($item->variables);

                // set render variable
                if (!isset($item->render)) {
                    $item->render = Joy_Render_Factory::get($item->action_extension);
                }

                // set request method
                $item->method = $_SERVER["REQUEST_METHOD"];                
                // controller exists
                $result = clone $item;

                if (Joy_Controller::exists($result->controller)) {
                    break;
                }
            }
            else {
                $result = null;
            }
        }

        return (is_null($result)) ? null : (new Joy_Router_Match($result));
    }
}

<?php

class Ajax{
    private $status = false;
    private $content = array();
    private $return_blocks = array();
    private $messages = array();

    public function start(){
        // set status
        if((!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
            $this->status = true;
            ob_start();
        }
    }

    public function setOptions($options){
        Json::set($options);
    }

//    public function setReturnBlocks($blocks, $structure){
//        $this->return_blocks = $blocks;
//
//        foreach($structure as $str_key => $str_value){
//            foreach($blocks as $block_key => $block_value){
//                if($this->recursiveInArray($block_value, $str_value)){
//                    unset($blocks[$block_key]);
//                    $blocks[$str_key] = $str_value;
//                }
//            }
//        }
//
//        $this->allow_blocks = $blocks;
//    }

    public function setReturnBlocks($blocks){
        $this->return_blocks = $blocks;
    }

    public function end(){
        global $messageStack;

        if($this->status){
            ob_end_clean();

            foreach($messageStack->messages as $message){
                Json::addMessage($message['text'], $message['class']);
            }

            $status = Json::get('status');
            if(empty($status))
                $status = isset($this->messages['error']) ? 'error' : 'success';

            Json::setJsonAndExit(array('content' => $this->content, 'status' => $status));
        }
        else{
            if(Json::get('status') == 'resubmit'){
                // use the curl class to resubmit the form
                require_once(DIR_WS_CLASSES.'curl.php');
                $c = new curl(Json::get('action'));
                $c->setopt(CURLOPT_FOLLOWLOCATION, true);
                $c->setopt(CURLOPT_POST, true);
                $c->setopt(CURLOPT_POSTFIELDS, $c->asPostString($_POST));
                echo $c->exec();
                if ($theError = $c->hasError())
                {
                  echo $theError;
                }
                $c->close();
                exit();
            }

//            foreach($this->content as $c)
//                echo $c;
        }
    }

    public function startBlock($block_name){
        if(!$this->status || !$this->checkReturnBlock($block_name))
            return false;

        ob_start();
        return true;
    }

    public function endBlock($block_name){
        if(!$this->status || !$this->checkReturnBlock($block_name))
            return false;

        $this->content[$block_name] = ob_get_contents();
        ob_end_clean();
        return true;
    }

    public function redirect($url, $redirect_type = null){
        if($this->status){
            Json::setJsonAndExit(array('status' => 'redirect', 'url' => $url,'redirect_type' => $redirect_type, 'content' => ''));
        }
        else
            zen_redirect($url);
    }

    public function getStatus(){
        return $this->status;
    }

    public function getContent(){
        while(Json::get('status') == 'resubmit'){

        }
    }

    public function getMessage(){

    }

    private function checkReturnBlock($block){
        if(empty($this->return_blocks) || in_array($block, $this->return_blocks))
            return true;
        return false;
    }

    public function loadLanguage($current_page_base){
        global $language_page_directory, $template, $template_dir_select;
        $directory_array = $template->get_template_part($language_page_directory . $template_dir_select, '/^'.$current_page_base . '/');
        while(list ($key, $value) = each($directory_array)) {
              echo "I AM LOADING: " . $language_page_directory . $template_dir_select . $value . '<br />';
              require_once($language_page_directory . $template_dir_select . $value);
        }

        // load master language file(s) if lang files loaded previously were "overrides" and not masters.
        if ($template_dir_select != '') {
              $directory_array = $template->get_template_part($language_page_directory, '/^'.$current_page_base . '/');
              while(list ($key, $value) = each($directory_array)) {
                //echo "I AM LOADING MASTER: " . $language_page_directory . $value.'<br />';
                require_once($language_page_directory . $value);
              }
        }
    }

    public function addMessage($class, $message, $type = 'error'){
        $this->_addMessage($class, $message, $type, false);
    }

    public function addSessionMessage($class, $message, $type = 'error'){
        $this->_addMessage($class, $message, $type, true);
    }

    private function _addMessage($class, $message, $type = 'error', $session = false){
        global $messageStack;
        $messageStack->add_session($class, $message, $type);
        $this->messages[$type] = array('class' => $class, 'message' => $message, 'session' => $session);
    }

}

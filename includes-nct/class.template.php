<?php

class Templater extends MainTemplater
{
    private $template;

    function __construct($template = null)
    {
		parent::__construct();
		foreach($GLOBALS as $key=>$values){
			$this->$key = $values;
		}
		/*echo "<pre>";
		print_r($GLOBALS);exit;*/

        if (isset($template))
        {
            $this->load($template);
        }
    }
	public function load($template)
	{

		/*
		 * This function loads the template file
		 */

		if (!is_file($template))
		{
			echo $template."file not found";
		   // throw new FileNotFoundException("File not found: $template");
		} elseif (!is_readable($template))
		{
			throw new IOException("Could not access file: $template");
		} else
		{
			$this->template = $template;
		}
	}
	public function set($var, $content)
	{
		$this->$var = $content;
	}
	public function get($var, $key)
	{
		$var=$this->$key;
	}
	public function publish($output = true)
	{
		/*
		 * Prints out the theme to the page
		 * However, before we do that, we need to remove every var witin {} that are not set
		 * @params
		 *  $output - whether to output the template to the screen or to just return the template
		 */
		ob_start();
		require $this->template;
		$content = ob_get_clean();
		print $content;
	}
	public function parse()
	{
		/*
		 * Function that just returns the template file so it can be reused
		 */
		ob_start();

		require $this->template;
		$content = ob_get_clean();
		return $content;
	}

}

?>
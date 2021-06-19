<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Dompdf\Dompdf;
 /**


* CodeIgniter PDF Library
 *
 * Generate PDF's in your CodeIgniter applications.
 *
 * @package         CodeIgniter
 * @subpackage      Libraries
 * @category        Libraries
 * @author          Chris Harvey
 * @license         MIT License
 * @link            https://github.com/chrisnharvey/CodeIgniter-PDF-Generator-Library



*/

require_once(dirname(__FILE__) . '/dompdf/autoload.inc.php');

class Pdf extends DOMPDF
{
    /**
     * Get an instance of CodeIgniter
     *
     * @access  protected
     * @return  void
     */
    protected function ci()
    {
        return get_instance();
    }

    /**
     * Load a CodeIgniter view into domPDF
     *
     * @access  public
     * @param   string  $view The view to load
     * @param   array   $data The view data
     * @return  void
     */
    public function load_view($view, $data = array())
    {   
        $html = $this->ci()->load->view($view, $data, TRUE);
        $this->load_html($html);
    }
    public function load_view2($name,$view, $data = array())
    {   
        
        $this->setPaper('A4', 'landscape');
        $html = $this->ci()->load->view($view, $data, TRUE);
        $this->load_html($html);
        $this->render();
        $this->stream($name."-PDS.pdf", array('Attachment'=> 0));
	}
	public function load_view4_portrait($name,$view, $data = array())
    {   
        $this->set_option('isRemoteEnabled', TRUE);
        $this->set_option('isHtml5ParserEnabled', TRUE);
        $this->setPaper('legal', 'landscape');
        $html = $this->ci()->load->view($view, $data, TRUE);
        $this->load_html($html);
        $this->render();
        $this->stream($name.".pdf", array('Attachment'=> 0));
    }
	public function load_view5_portrait($name,$view, $data = array())
    {   
		
		// $customPaper = array(0, 0, 147.40, 209.76);
		// $customPaper = array(0,0,279,300);
		$customPaper = array(0,0,279,368);
		// $dompdf->set_paper($customPaper);
		$this->setPaper($customPaper);
		// $this->setPaper('Continuous', 'portrait');
		// $this->setPaper('Continuous', 'portrait');
        $html = $this->ci()->load->view($view, $data, TRUE);
        $this->load_html($html);
        $this->render();
        $this->stream($name.".pdf", array('Attachment'=> 0));
    }
}
?>

<?php
// PDFG Shortcode
function pdfg_shortcode_func( $atts, $content = null ) {
    $args = shortcode_atts( array(
        'title' => ''
    ), $atts );
	
	ob_start();
	?>
	<div class="pdfg-shortcode">
		<div class="pdfg-wrap">
			<a class="pdfg-print" title="<?php _e('Print','pdfg'); ?>" href="javascript:void(0)"></a>
		</div>
		<div class="pdfg-content"><?php echo $content; ?></div>
		<form name="pdfg-form" class="pdfg-form" method="post" action="" style="display:none;">
			<textarea name="pdfg_print_html" class="pdfg-html"></textarea>
		</form>
	</div>
	<?php
    return ob_get_clean();
}
add_shortcode( 'pdfg_shortcode', 'pdfg_shortcode_func' );

if(isset($_POST['pdfg_print_html']))
{
	$header_margin   = 30;
	$_pdf_filename   = get_bloginfo('name').time().'.pdf';
	$pdfg_print_html = stripslashes(html_entity_decode($_POST['pdfg_print_html']));
	
	// Extend the TCPDF class to create custom Header and Footer
	class PDFG extends TCPDF 
	{
		//PDF Page header
		public function Header() 
		{
			$blogname  = get_bloginfo('title');
			$titleHtml = '<h3 style="color:#333333;font-weight:bold;font-size:16px;">'.$blogname.'</h3><hr/>';
			$this->SetFont('helvetica', '', 10);
			$this->writeHTML($titleHtml);
		}

		// PDF Page footer
		public function Footer() 
		{
			$blogurl = get_bloginfo('url');
			// Position at 30 mm from bottom
			$this->SetY(-15);
		
			// Set font
			$this->SetFont('helvetica', '', 8);
			
			$footerHtml = '<table border="1" cellpadding="5" cellspacing="0" width="100%">
								<tr>
									<td colspan="2" style="color:#2D2E2E;text-align:center;"><i>'.$blogurl.'</i></td>
								</tr>								
							</table><div style="color:#2D2E2E;text-align:center;line-height:3;">Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages().'</div>';
			$this->WriteHTML($footerHtml, true, 0, true, 0);
		}
	}
	
	$pdf = new PDFG(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$_marginTop = $header_margin*0.352777778;  // convert "pixel" value into "mm" (1px = 0.352777778mm for 72dpi)
	$pdf->SetMargins(PDF_MARGIN_LEFT, $_marginTop+8, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, 22);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}

	// ---------------------------------------------------------

	// add a page
	$pdf->AddPage('L', 'A4');
	$pdf->SetFont('helvetica', '', 10);
	$pdf->SetTextColor(0, 0, 0); 

ob_start();
$pdfHtml .= <<<EOD
$pdfg_print_html
EOD;

	$pdf->writeHTML($pdfHtml, true, false, false, false, '');
	ob_end_clean();
	$pdf->Output($_pdf_filename,'D');
}
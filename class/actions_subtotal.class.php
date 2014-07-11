<?php
class ActionsSubtotal
{ 
     /** Overloading the doActions function : replacing the parent's function with the one below 
      *  @param      parameters  meta datas of the hook (context, etc...) 
      *  @param      object             the object you want to process (an invoice if you are in invoice module, a propale in propale's module, etc...) 
      *  @param      action             current action (if set). Generally create or edit or null 
      *  @return       void 
      */
     var $module_number = 104777;
 
      
    function formObjectOptions($parameters, &$object, &$action, $hookmanager) 
    {  
      	global $langs,$db,$user, $conf;
		
		$langs->load('subtotal@subtotal');
		
		$contexts = explode(':',$parameters['context']);
		
		if(in_array('ordercard',$contexts) || in_array('propalcard',$contexts) || in_array('invoicecard',$contexts)) {
        		
        	if ($object->statut == 0  && $user->rights->{$object->element}->creer) {
			
			
				if($object->element=='facture')$idvar = 'facid';
				else $idvar='id';
				
				
				if($action=='add_title_line' || $action=='add_total_line' || $action=='add_subtitle_line' || $action=='add_subtotal_line') {
					
					if($action=='add_title_line') {
						$title = GETPOST('title');
						if(empty($title)) $title = $langs->trans('title');
						$qty = 1;
					}
					else if($action=='add_subtitle_line') {
						$title = GETPOST('title');
						if(empty($title)) $title = $langs->trans('subtitle');
						$qty = 2;
					}
					else if($action=='add_subtotal_line') {
						$title = $langs->trans('SubSubTotal');
						$qty = 98;
					}
					else {
						$title = $langs->trans('SubTotal');
						$qty = 99;
					}
					
	    			if( (float)DOL_VERSION <= 3.4 ) {
						if($object->element=='facture') $object->addline($object->id, $title, 0,$qty,0,0,0,0,0,'','',0,0,'','HT',0,9,-1, $this->module_number);
						else if($object->element=='propal') $object->addline($object->id,$title, 0,$qty,0,0,0,0,0,'HT',0,0,9,-1, $this->module_number);
						else if($object->element=='commande') $object->addline($object->id,$title, 0,$qty,0,0,0,0,0,0,0,'HT',0,'','',9,-1, $this->module_number);
						
					}
					else {
						if($object->element=='facture') $object->addline($title, 0,$qty,0,0,0,0,0,'','',0,0,'','HT',0,9,-1, $this->module_number);
						else if($object->element=='propal') $object->addline($title, 0,$qty,0,0,0,0,0,'HT',0,0,9,-1, $this->module_number);
						else if($object->element=='commande') $object->addline($title, 0,$qty,0,0,0,0,0,0,0,'HT',0,'','',9,-1, $this->module_number);
												
					}
				}
				else if($action==='ask_deleteallline') {
						$form=new Form($db);
						
						$lineid = GETPOST('lineid','integer');
						$TIdForGroup = $this->getArrayOfLineForAGroup($object, $lineid);
					
						$nbLines = count($TIdForGroup);
					
						$formconfirm=$form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$lineid, $langs->trans('deleteWithAllLines'), $langs->trans('ConfirmDeleteAllThisLines',$nbLines), 'confirm_delete_all_lines','',0,1);
						print $formconfirm;
				}

				
				    	
				?><script type="text/javascript">
					$(document).ready(function() {
						
						<?php
							if($conf->global->SUBTOTAL_MANAGE_SUBSUBTOTAL==1) {
								?>$('div.fiche div.tabsAction').append('<br /><br />');<?php
							}
						?>
						
						$('div.fiche div.tabsAction').append('<div class="inline-block divButAction"><a id="add_title_line" href="javascript:;" class="butAction"><?php echo  $langs->trans('AddTitle' )?></a></div>');
						$('div.fiche div.tabsAction').append('<div class="inline-block divButAction"><a id="add_total_line" href="javascript:;" class="butAction"><?php echo  $langs->trans('AddSubTotal')?></a></div>');

						<?php
							if($conf->global->SUBTOTAL_MANAGE_SUBSUBTOTAL==1) {
							?>
								$('div.fiche div.tabsAction').append('<div class="inline-block divButAction"><a id="add_subtitle_line" href="javascript:;" class="butAction"><?php echo  $langs->trans('AddSubTitle' )?></a></div>');
								$('div.fiche div.tabsAction').append('<div class="inline-block divButAction"><a id="add_subtotal_line" href="javascript:;" class="butAction"><?php echo  $langs->trans('AddSubSubTotal')?></a></div>');
		
							<?php								
							}
						?>
						
						$('#add_title_line').click(function() {
							
							var titre = window.prompt("<?php echo $langs->trans('YourTitleLabel') ?>", "<?php echo $langs->trans('title') ?>");
							
							if(titre!=null) {
								
								$.get('?<?php echo $idvar ?>=<?php echo $object->id ?>&action=add_title_line&title='+encodeURIComponent(titre), function() {
									document.location.href='?<?php echo $idvar ?>=<?php echo $object->id ?>';
								});
								
								
							}
							
						});
						$('#add_subtitle_line').click(function() {
							var titre = window.prompt("<?php echo $langs->trans('YourTitleLabel') ?>", "<?php echo $langs->trans('title') ?>");
							
							if(titre!=null) {
								
								$.get('?<?php echo $idvar ?>=<?php echo $object->id ?>&action=add_subtitle_line&title='+encodeURIComponent(titre), function() {
									document.location.href='?<?php echo $idvar ?>=<?php echo $object->id ?>';
								});
								
								
							}
							
							
						});
						
						$('#add_total_line').click(function() {
							
							$.get('?<?php echo $idvar ?>=<?php echo $object->id ?>&action=add_total_line', function() {
								document.location.href='?<?php echo $idvar ?>=<?php echo $object->id ?>';
							});
							
						});
						
						$('#add_subtotal_line').click(function() {
							
							$.get('?<?php echo $idvar ?>=<?php echo $object->id ?>&action=add_subtotal_line', function() {
								document.location.href='?<?php echo $idvar ?>=<?php echo $object->id ?>';
							});
							
						});
						
						
					});
					
				</script><?php
			}
		}

		return 0;
	}
     
	function formBuilddocOptions($parameters) {
	/* Réponse besoin client */		
			
		global $conf, $langs, $bc, $var;
			
		$action = GETPOST('action');	
			
		if (
				in_array('invoicecard',explode(':',$parameters['context']))
				|| in_array('propalcard',explode(':',$parameters['context']))
				|| in_array('ordercard',explode(':',$parameters['context']))
			)
	        {	
				$hideInnerLines	= isset( $_SESSION['subtotal_hideInnerLines_'.$parameters['modulepart']] ) ?  $_SESSION['subtotal_hideInnerLines_'.$parameters['modulepart']] : 0;
				$hidedetails	= isset( $_SESSION['subtotal_hidedetails_'.$parameters['modulepart']] ) ?  $_SESSION['subtotal_hidedetails_'.$parameters['modulepart']] : 0;	
					
					
		     	/*$out.= '<tr '.$bc[$var].'>
		     			<td colspan="4" align="right">
		     				<label for="hideInnerLines">'.$langs->trans('HideInnerLines').'</label>
		     				<input type="checkbox" id="hideInnerLines" name="hideInnerLines" value="1" '.(( $hideInnerLines ) ? 'checked="checked"' : '' ).' />
		     			</td>
		     			</tr>';
				$var = -$var;
				 
				 */
				
				$out.= '<tr '.$bc[$var].'>
		     			<td colspan="4" align="right">
		     				<label for="hidedetails">'.$langs->trans('SubTotalhidedetails').'</label>
		     				<input type="checkbox" id="hidedetails" name="hidedetails" value="1" '.(( $hidedetails ) ? 'checked="checked"' : '' ).' />
		     			</td>
		     			</tr>';
				$var = -$var;
				 
				
				
				$this->resprints = $out;	
			}
			
		
        return 1;
	} 
	 
    function formEditProductOptions($parameters, &$object, &$action, $hookmanager) 
    {
		
    	if (in_array('invoicecard',explode(':',$parameters['context'])))
        {
        	
        }
		
        return 0;
    }
	
	function doActions($parameters, &$object, $action, $hookmanager) {
		if($action === 'builddoc') {
			
			if (
				in_array('invoicecard',explode(':',$parameters['context']))
				|| in_array('propalcard',explode(':',$parameters['context']))
				|| in_array('ordercard',explode(':',$parameters['context']))
			)
	        {
	        	
				if(in_array('invoicecard',explode(':',$parameters['context']))) {
					$sessname = 'subtotal_hideInnerLines_facture';	
					$sessname2 = 'subtotal_hidedetails_facture';
				}
				elseif(in_array('propalcard',explode(':',$parameters['context']))) {
					$sessname = 'subtotal_hideInnerLines_propal';
					$sessname2 = 'subtotal_hidedetails_propal';	
				}
				elseif(in_array('ordercard',explode(':',$parameters['context']))) {
					$sessname = 'subtotal_hideInnerLines_commande';
					$sessname2 = 'subtotal_hidedetails_commande';	
				}
				else {
					$sessname = 'subtotal_hideInnerLines_unknown';
					$sessname2 = 'subtotal_hidedetails_unknown';
				}
								
		
				$hideInnerLines = (int)isset($_REQUEST['hideInnerLines']);	
				$_SESSION[$sessname] = $hideInnerLines;		
				
				$hidedetails= (int)isset($_REQUEST['hidedetails']);	
				$_SESSION[$sessname2] = $hidedetails;			
	    
				
				
	           	foreach($object->lines as &$line) {
	        		
					if ($line->product_type == 9 && $line->special_code == $this->module_number) {
						$line->total_ht = $this->getTotalLineFromObject($object, $line);
					}
	        	}
	        }
			
		}
		else if($action === 'confirm_delete_all_lines' && GETPOST('confirm')=='yes') {
			
			$Tab = $this->getArrayOfLineForAGroup($object, GETPOST('lineid'));
			
			foreach($Tab as $idLine) {
				
					if($object->element=='facture') $object->deleteline($idLine);
					else if($object->element=='propal') $object->deleteline($idLine);
					else if($object->element=='commande') $object->deleteline($idLine);
			}
			
			header('location:?id='.$object->id);
			exit;
			
		}

		return 0;
	}
	
	function formAddObjectLine ($parameters, &$object, &$action, $hookmanager) {
		
		return 0;
	}

	function getArrayOfLineForAGroup(&$object, $lineid) {
		
		$rang = $line->rang;
		$qty_line = $line->qty;
		
		$total = 0;
		
		$found = false;
		
		$Tab= array();
		
		foreach($object->lines as $l) {
		
			if($l->rowid == $lineid) {
				$found = true;
				$qty_line = $l->qty;
			}
			
			if($found) {
				
				$Tab[] = $l->rowid;
				
				if($l->special_code==$this->module_number && (($l->qty==99 && $qty_line==1) || ($l->qty==98 && $qty_line==2))   ) {
					break; // end of story
				}
			}
			
			
		}
		
		
		return $Tab;
		
	}

	function getTotalLineFromObject(&$object, &$line) {
		
		$rang = $line->rang;
		$qty_line = $line->qty;
		
		$total = 0;
		foreach($object->lines as $l) {
			//print $l->rang.'>='.$rang.' '.$total.'<br/>';
			if($l->rang>=$rang) {
				//echo 'return!<br>';
				return $total;
			} 
			else if($l->special_code==$this->module_number && (($l->qty==1 && $qty_line==99) || ($l->qty==2 && $qty_line==98))   ) {
				$total = 0;
			}
			else {
				$total +=$l->total_ht;	
			}
			
			
		}
		
		
		return $total;
	}
	
	function pdf_add_total(&$pdf,&$object, &$line, $label, $description,$posx, $posy, $w, $h) {
		$pdf->SetXY ($posx, $posy);
		

		$hideInnerLines = (int)isset($_REQUEST['hideInnerLines']);	
		if(!$hideInnerLines) {

			if($line->qty==99)	$pdf->SetFillColor(230,230,230);
			else 	$pdf->SetFillColor(240,240,240);
		
		
			$pdf->MultiCell(200-$posx, $h, '', 0, '', 1);
			
		}
				
		if($hideInnerLines) {
			$pdf->SetFont('', '', 9);
		}
		else {
			$pdf->SetFont('', 'B', 9);
		}
		
		$pdf->SetXY ($posx, $posy);
		$pdf->MultiCell($w, $h, $label.' ', 0, 'R');
		
		$total = $this->getTotalLineFromObject($object, $line);
		
		$line->total_ht = $total;
		$line->total = $total;
		$pdf->SetXY($pdf->postotalht, $posy);
		$pdf->MultiCell($pdf->page_largeur-$pdf->marge_droite-$pdf->postotalht, 3, price($total), 0, 'R', 0);
	}
	function pdf_add_title(&$pdf,&$object, &$line, $label, $description,$posx, $posy, $w, $h) {
			
		$pdf->SetXY ($posx, $posy);
		
		$hideInnerLines = (int)isset($_REQUEST['hideInnerLines']);	
		if($hideInnerLines) {

			if($line->qty==1)$pdf->SetFont('', '', 9);
			else $pdf->SetFont('', 'I', 9);
			
		}
		else {

			if($line->qty==1)$pdf->SetFont('', 'BU', 9);
			else $pdf->SetFont('', 'BUI', 9);
			
		}
		
		$pdf->MultiCell($w, $h, $label, 0, 'L');
		
		if($description && !$hidedesc) {
			$posy = $pdf->GetY();
			
			$pdf->SetFont('', '', 8);
			
			$pdf->writeHTMLCell($w, $h, $posx, $posy, $description, 0, 1, false, true, 'J',true);
			
			
			
		}
	}

	function pdf_writelinedesc_ref($parameters=false, &$object, &$action='') {
	// ultimate PDF hook O_o
		
		return $this->pdf_writelinedesc($parameters,$object,$action);
		
	}

	function pdf_writelinedesc($parameters=false, &$object, &$action='')
	{

		foreach($parameters as $key=>$value) {
			${$key} = $value;
		}
		
		$hideInnerLines = (int)isset($_REQUEST['hideInnerLines']);	
		$hidedetails = (int)isset($_REQUEST['hidedetails']);	
		
		if($object->lines[$i]->special_code == $this->module_number) {
			if ($hideInnerLines) { // si c une ligne de titre
		    	$fk_parent_line=0;
				foreach($object->lines as $k=>&$line) {
					if($hideInnerLines) {
						if($line->product_type==9 && $line->rowid>0) $fk_parent_line = $line->rowid;
					
						if ($line->product_type != 9) { // jusqu'au prochain titre ou total
							$line->fk_parent_line = $fk_parent_line;
				
						}
						

					}

					if($hideTotal) {
						$line->total = 0;
						$line->subprice= 0;
					}

				}
		    }
		}
	   
	   if ($object->lines[$i]->product_type == 9) {
			
			
			if($object->lines[$i]->special_code == $this->module_number) {
				
				
				$line = &$object->lines[$i];
					
				
			
				if($line->pagebreak) {
				//	$pdf->addPage();
				//	$posy = $pdf->GetY();
				}
				
				if($line->label=='') {
					$label = $outputlangs->convToOutputCharset($line->desc);
					$description='';
				}
				else {
					$label = $outputlangs->convToOutputCharset($line->label);
					$description=$outputlangs->convToOutputCharset(dol_htmlentitiesbr($line->desc));
				}
				
				if($line->qty>90) {
					$pageBefore = $pdf->getPage();
					$this->pdf_add_total($pdf,$object, $line, $label, $description,$posx, $posy, $w, $h);
					$pageAfter = $pdf->getPage();	

					if($pageAfter>$pageBefore) {
						$pdf->rollbackTransaction(true);	
						$pdf->addPage();
						$posy = $pdf->GetY();
						$this->pdf_add_total($pdf,$object, $line, $label, $description,$posx, $posy, $w, $h);
						$posy = $pdf->GetY();
					}

				}	
				else{
					$pageBefore = $pdf->getPage();
						
					$this->pdf_add_title($pdf,$object, $line, $label, $description,$posx, $posy, $w, $h); 
					$pageAfter = $pdf->getPage();	

					if($pageAfter>$pageBefore) {
						$pdf->rollbackTransaction(true);
						$pdf->addPage();
						$posy = $pdf->GetY();
						$this->pdf_add_title($pdf,$object, $line, $label, $description,$posx, $posy, $w, $h);
						$posy = $pdf->GetY();
					}
					
					
				}
	
				
				
	
			}
			
		}
		else {
			
			if($hideInnerLines) {
				$pdf->rollbackTransaction(true);
			}
			else {
				$labelproductservice=pdf_getlinedesc($object, $i, $outputlangs, $hideref, $hidedesc, $issupplierline);
				$pdf->writeHTMLCell($w, $h, $posx, $posy, $outputlangs->convToOutputCharset($labelproductservice), 0, 1);
			}
			
		}


		return 1;
	}
	
	

	function printObjectLine ($parameters, &$object, &$action, $hookmanager){
		
		global $conf,$langs,$user;
		
		$num = &$parameters['num'];
		$line = &$parameters['line'];
		$i = &$parameters['i'];

		$contexts = explode(':',$parameters['context']);
	
		if($line->special_code!=$this->module_number) {
			null;
		}	
		else if (in_array('invoicecard',$contexts) || in_array('propalcard',$contexts) || in_array('ordercard',$contexts)) 
        {
        	
			if($object->element=='facture')$idvar = 'facid';
			else $idvar='id';
					
					if($action=='savelinetitle' && $_POST['lineid']===$line->id) {
						
						$description = ($line->qty==99) ? '' : $_POST['linedescription'];
						
						if($object->element=='facture') $object->updateline($line->id,$description, 0,$line->qty,0,'','',0,0,0,'HT',0,9,0,0,null,0,$_POST['linetitle'], $this->module_number);
						else if($object->element=='propal') $object->updateline($line->id, 0,$line->qty,0,0,0,0, $description ,'HT',0,$this->module_number,0,0,0,0,$_POST['linetitle'],9);
						else if($object->element=='commande') $object->updateline($line->id,$description, 0,$line->qty,0,0,0,0,'HT',0,'','',9,0,0,null,0,$_POST['linetitle'], $this->module_number);
						
					}
					else if($action=='editlinetitle') {
						?>
						<script type="text/javascript">
							$(document).ready(function() {
								$('#addproduct').submit(function () {
									$('input[name=saveEditlinetitle]').click();
									return false;
								}) ;
							});
							
						</script>
						<?php
					}
					else {
						if( (float)DOL_VERSION <= 3.4 ) {
							
							?>
							<script type="text/javascript">
								$(document).ready(function() {
									$('#tablelines tr[rel=subtotal]').mouseleave(function() {
										
										id_line =$(this).attr('id');
										
										$(this).find('td[rel=subtotal_total]').each(function() {
											$.get(document.location.href, function(data) {
												var total = $(data).find('#tablelines tr#'+id_line+' td[rel=subtotal_total]').html();
												
												$('#tablelines tr#'+id_line+' td[rel=subtotal_total]').html(total);
												
											});
										});
									});
								});
								
							</script>
							<?php
							
						}
					}
					
					if(empty($line->description)) $line->description = $line->desc;
					
					/* Titre */
					//var_dump($line);
					?>
					<tr class="drag drop" rel="subtotal" id="row-<?php echo $line->id ?>" style="<?php
						   if($line->qty==99) print 'background-color:#ddffdd';
						   else if($line->qty==98) print 'background-color:#ddddff;';
						   else if($line->qty==2) print 'background-color:#eeeeff; ';
						   else print 'background-color:#eeffee;' ;
						   
					?>;">
					<td colspan="5" style="font-weight:bold;  <?php echo ($line->qty>90)?'text-align:right':' font-style: italic;' ?> "><?php
					
							if($action=='editlinetitle' && $_REQUEST['lineid']===$line->id ) {
								
								if($line->qty<=1) print img_picto('', 'subtotal@subtotal');
								else if($line->qty==2) print img_picto('', 'subsubtotal@subtotal').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
								
								if($line->label=='') {
									$line->label = $line->description;
									$line->description='';
								}
								
								?>
								<input type="text" name="line-title" id-line="<?php echo $line->id ?>" value="<?php echo $line->label ?>" size="80" /><br />
								<?php
								
								if($line->qty<10) {
									?>
									<textarea name="line-description" id-line="<?php echo $line->id ?>" cols="70" rows="2" /><?php echo $line->description ?></textarea>
									<?php
								}
								
							}
							else {
								
							     if($line->qty<=1) print img_picto('', 'subtotal@subtotal');
								 else if($line->qty==2) print img_picto('', 'subsubtotal@subtotal').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
								
								 if (empty($line->label)) {
								 	print  $line->description;
								 } 
								 else {
								 	print '<span class="classfortooltip" title="'.$line->description.'">'.$line->label.'</span>';
								 } 
								
								 if($line->qty>90) { print ' : '; }
								 
								
							}
					 ?></td><?php
					  if (! empty($conf->margin->enabled) && empty($user->societe_id)) {
						 ?><td align="right" class="nowrap">&nbsp;</td>
					  	<?php if (! empty($conf->global->DISPLAY_MARGIN_RATES) && $user->rights->margins->liretous) {?>
					  	  <td align="right" class="nowrap">&nbsp;</td>
					  	<?php
					  }
					  if (! empty($conf->global->DISPLAY_MARK_RATES) && $user->rights->margins->liretous) {?>
					  	  <td align="right" class="nowrap">&nbsp;</td>
					  <?php } } ?>
					 
					  <?php	
						
							 if($line->qty>90) {
							/* Total */
								$total_line = $this->getTotalLineFromObject($object, $line);
								?>
								<td align="right" style="font-weight:bold;" rel="subtotal_total"><?php echo price($total_line) ?></td>
								<?php
								
							}
							 else {
							 	
								?>
								<td>&nbsp;</td>
								<?php
							 }	
						?>
					
					<td align="center">
						<?php
							if($action=='editlinetitle' && $_REQUEST['lineid']==$line->id ) {
								?>
								<input class="button" type="button" name="saveEditlinetitle" value="<?php echo $langs->trans('Save') ?>" />
								<script type="text/javascript">
									$(document).ready(function() {
										$('input[name=saveEditlinetitle]').click(function () {
											
											$.post("<?php echo '?'.$idvar.'='.$object->id ?>",{
													action:'savelinetitle'
													,lineid:<?php echo $line->id ?>
													,linetitle:$('input[name=line-title]').val()
													,linedescription:$('textarea[name=line-description]').val()
											}
											,function() {
												document.location.href="<?php echo '?'.$idvar.'='.$object->id ?>";	
											});
											
										});
										
										$('input[name=cancelEditlinetitle]').click(function () {
											document.location.href="<?php echo '?'.$idvar.'='.$object->id ?>";
										});
										
									});
									
								</script>
								<?php
							}
							else{
								
								if ($object->statut == 0  && $user->rights->{$object->element}->creer) {
								
								?>
									<a href="<?php echo '?'.$idvar.'='.$object->id.'&action=editlinetitle&lineid='.$line->id ?>">
										<?php echo img_edit() ?>		
									</a>
								<?php
								
								}								
							}
						?>
					</td>

					<td align="center" nowrap="nowrap">	
						<?php
							if($action=='editlinetitle' && $_REQUEST['lineid']===$line->id ) {
								?>
								<input class="button" type="button" name="cancelEditlinetitle" value="<?php echo $langs->trans('Cancel') ?>" />
								<?php
							}
							else{
								if ($object->statut == 0  && $user->rights->{$object->element}->creer) {
								
								?>
									<a href="<?php echo '?'.$idvar.'='.$object->id.'&action=ask_deleteline&lineid='.$line->id ?>">
										<?php echo img_delete() ?>		
									</a>
								<?php								
								
								}
								
								if($line->qty<10) {
									
								?><a href="<?php echo '?'.$idvar.'='.$object->id.'&action=ask_deleteallline&lineid='.$line->id ?>">
										<?php echo img_picto('deleteWithAllLines', 'delete_all.png@subtotal', $other) ?>		
									</a><?php								
								}
																	
							}
						?>	
						
					</td>

					<?php if ($num > 1 && empty($conf->browser->phone)) { ?>
					<td align="center" class="tdlineupdown">
						<?php if ($i > 0 && ($object->statut == 0  && $user->rights->{$object->element}->creer)) { ?>
						<a class="lineupdown" href="<?php echo '?'.$idvar.'='.$object->id.'&amp;action=up&amp;rowid='.$line->id ?>">
						<?php echo img_up(); ?>
						</a>
						<?php } ?>
						<?php if ($i < $num-1 && ($object->statut == 0  && $user->rights->{$object->element}->creer)) { ?>
						<a class="lineupdown" href="<?php echo '?'.$idvar.'='.$object->id.'&amp;action=down&amp;rowid='.$line->id ?>">
						<?php echo img_down(); ?>
						</a>
						<?php } ?>
					</td>
				    <?php } else { ?>
				    <td align="center"<?php echo ((empty($conf->browser->phone) && ($object->statut == 0  && $user->rights->{$object->element}->creer))?' class="tdlineupdown"':''); ?>></td>
					<?php } ?>

					</tr>
					<?php
					
					
				
			
		}
		
		return 0;

	}
}
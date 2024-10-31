<?php
	// Setup versions
	$versions = array(
		// English (US)
		"AMP" => "Amplified Bible",
		"CEV" => "Contemporary English Version (US Version)",
		"CEVD" => "Contemporary English Version (US Version)",
		"ESV" => "English Standard Version",
		"GNTD" => "Good News Translation (US Version)",
		"KJVA" => "King James Version with Apocrypha, American Edition",
		"MSG" => "The Message",
		"NABRE" => "New American Bible, Revised Edition",
		"NASB" => "New American Standard Bible",
		"NIV" => "New International Version",
		"NLT" => "New Living Translation",
		"NRSV" => "New Revised Standard Version",
		"RSV" => "Revised Standard Version",
		
		// English (British)
		"CEVUK" => "Contemporary English Version (Anglicised Version)",
		"DARBY" => "Darby Translation 1890",
		"GNBDC" => "Good News Bible (Interconfessional Edition with Deuterocanonical books/Apocrypha)",
		"GWC" => "The Trench Epistles by Gerald Warre (Cornish) 1915",
		"KJV" => "King James Version",
		"RV1885" => "Revised Version 1885",
		
		// Español (Spanish)
		"BHTI" => "La Biblia Hispanoamericana (Traducción Interconfesional, versión hispanoamericana)",
		"BLP" => "La Palabra (versión española)", 
		"BLPH" => "La Palabra (versión hispanoamericana)",
		"BTI" => "La Biblia, Traducción Interconfesional (versión española)",
		"DHH" => "Biblia Dios Habla Hoy (sin notas ni ayudas)",
		"DHH" => "Dios Habla Hoy Versión Española",
		"RVC" => "Reina Valera Contemporánea",
		"RVR60" => "Biblia Reina Valera 1960",
		"RVR95" => "Biblia Reina Valera 1995",
		"TLA" => "Traducción en Lenguaje Actual",
		
		// 中文 (Chinese)
		"CUNP" => "新標點和合本, 上帝版（繁體字）(CUNP – Shangti)",
		"CUNPSS" => "新标点和合本, 上帝版（简体字）(CUNPSS – Shangti)",
		"RCUV" => "和合本修訂版（繁體字）(RCUV)",
		"RCUVSS" => "和合本修订版（简体字）(RCUVSS)",
		
		// 한국어 (Korean)
		"RNKSV" => "Revised New Korean Standard Version (개역개정) 성경전서 개역개정판",
			
	);

?>

<div class="wrap">
	<!--
	<div style="float: right; width: 460px; text-align: right; margin-top: 10px;">
		<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2FOhSnapPlugin&amp;width=100&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=21&amp;appId=481896828610378" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe><a href="https://twitter.com/OhSnapPlugin" class="twitter-follow-button" data-show-count="false">Follow @OhSnapPlugin</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
	</div>
	-->
	<h2 style="border-bottom: 1px solid #ccc;">
		<span style=""><?php _e('Scripture Highlighter', 'hvst_scripthigh'); ?></span> <span style="font-size: 0.8em">Settings</span>
	</h2>
	
	
	
	<div style="text-align: right;">A <span style="font-family: 'Arvo', serif; font-weight: bold;">Harvest</span><span style="font-family: 'Arvo', serif;">Sites</span> product</div>
	<form method="post" action="options.php">
 
		<?php settings_fields('hvst_scripthigh_settings_group'); ?>
 
		<table class="form-table">
			<tbody>
				<tr valign="top">	
					<th scope="row" valign="top">
						<?php _e('Disable Auto Highlight', 'hvst_scripthigh'); ?>
					</th>
					<td>
						<input id="hvst_scripthigh_settings[autoparse_disabled]" name="hvst_scripthigh_settings[autoparse_disabled]" type="checkbox" value="1" <?php checked(1, $scripthigh_settings['autoparse_disabled']); ?> />
						<label class="description" for="hvst_scripthigh_settings[autoparse_disabled]"><?php _e('Check this to disable automatic Scripture Highlighting', 'hvst_scripthigh'); ?></label>
					</td>
				</tr>
				
				<tr valign="top">	
					<th scope="row" valign="top">
						<?php _e('Version', 'hvst_scripthigh'); ?>
					</th>
					<td>
						<select name="hvst_scripthigh_settings[version]">
							<option value="" <?=($scripthigh_settings['version'] == "") ? 'selected' : ''?> >-- Default Version --</option>
							<?php foreach ($versions as $code => $name): ?>
								<?php
									$title = mb_substr($name, 0, 25);
									
									if (strlen($title) < strlen($name)) {
										$title = $code . ' - ' . $title . '...';
									} else {
										$title = $code . ' - ' . $name;
									}
								?>
							<option value="<?=$code?>" <?=($scripthigh_settings['version'] == $code) ? 'selected' : ''?> ><?=$title?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				
			</tbody>
		</table>
		<br />
		<br />
		
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Options', 'mfwp_domain'); ?>" />
		</p>
 
	</form>
</div>
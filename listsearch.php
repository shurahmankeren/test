	<?php if ($id == "history") { ?>
	<script>
		function animate()
		{			
			jQuery("#isanimate").val("1");
			document.frmsearch.target = "_blank";
			document.frmsearch.submit();
		}
		
		jQuery(document).ready(
			function()
			{
				<?php if (count($data) > 0) { ?>
					jQuery("#tdmap").show();
					init();

					var center = new OpenLayers.LonLat(<?=$data[0]->gps_longitude_real_fmt;?>, <?=$data[0]->gps_latitude_real_fmt;?>);

					map.setCenter(center.transform
						(
                    		new OpenLayers.Projection("EPSG:4326"),
                    		map.getProjectionObject()
                		), <?=$this->config->item('zoom_history')?>);


				p = "";
				<?php for($i=0; $i < count($data); $i++) { ?>
					<?php if ($i >= 100) break; ?>
					p += "&lon[]=<?=$data[$i]->gps_longitude_real_fmt;?>&lat[]=<?=$data[$i]->gps_latitude_real_fmt;?>";
				<?php } ?>
				track("<?php echo $this->lang->line('ltrack'); ?>: <?php echo $vehicle->vehicle_no; ?> <?php echo $vehicle->vehicle_name; ?>", p);


				var ref = "";
				
				ref += "<a href='javascript:animate()'><font color='#000000'>[ <?php echo $this->lang->line("lanimation"); ?> ]</font></a>";
				ref += '<a href="<?=base_url();?>map/historyfull?dummy=on&vehicle=<?=$vehicle->vehicle_id;?>';
				<?php for($i=0; $i < count($data); $i++) { ?>
					<?php if ($i >= 100) break; ?>
					addMarker(<?=$i+1?>, '<?=$data[$i]->gps_longitude_real_fmt;?>', '<?=$data[$i]->gps_latitude_real_fmt;?>', <?=$vehicle->vehicle_id;?>);
					ref += '&lnglat[]=' + '<?=$data[$i]->gps_longitude_real_fmt;?>,<?=$data[$i]->gps_latitude_real_fmt;?>';
				<?php } ?>

				ref += '" target="_blank"><font color="#000000">[ <?=$this->lang->line('lfull_size');?> ]</font></a>';
				jQuery("#refmap").html(ref);

				<?php } ?>
			}
		);

	    function track(no, p)
	    {
			var lgpx = new OpenLayers.Layer.GML(no, "<?=base_url()?>map/gpx?"+p, 
				{
					format: OpenLayers.Format.GPX,
					style: {strokeColor: "#FF0000", strokeWidth: 4, strokeOpacity: 0.9},
					projection: new OpenLayers.Projection("EPSG:4326")
				}
			);
			map.addLayer(lgpx);	    	
	    }        

       function addMarker(no, lng, lat, id)
        {
			var kml_tracker5 = new OpenLayers.Layer.GML
			(
    			no,
    			"<?=base_url()?>map/kmllastcoord/"+lng+"/"+lat+"/"+id+"/off/on",
    			{
        			format: OpenLayers.Format.KML,
        			projection: new OpenLayers.Projection("EPSG:4326"),
        			formatOptions:
        			{
          				extractStyles: true,
          				extractAttributes: true,
          				maxDepth: 2
        			}
    			}
			);

			map.addLayer(kml_tracker5);

			var center = new OpenLayers.LonLat(lng, lat);
			center.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());

			kml_tracker5.size = new OpenLayers.Size(-11, -30);

			popup = new OpenLayers.Popup.FramedCloud(
				"featurePopup"
				, center
				, new OpenLayers.Size(48, 33)
				, "<div id='pup'>" + no + "</div>"
				, kml_tracker5
				, false,
                null
			);

            popup.autoSize = true;
            popup.calculateRelativePosition = function(){
                   return 'tr';
               }
            var popup = map.addPopup(popup);

        }
	</script>
	<?php } ?>
	<?php if ($id == "history") { ?>
		<?php if (! $isgtp) { ?>
		<h3>Odometer: <b><?=$totalodometer;?> km</b></h3>
		<?php } else { ?>
		<!--
			<p><?php echo $this->lang->line("ltotal_odometer"); ?> <?php echo $this->lang->line("luntil1"); ?> <?php echo date("d/m/Y H:i:s", $this->period2); ?> <b><?=$odometer;?> km</b></p>
		-->
		<p><?php echo $this->lang->line("lodometer"); ?> <?php echo date("d/m/Y H:i:s", $this->period1); ?> - <?php echo date("d/m/Y H:i:s", $this->period2); ?> <b><? echo ($totalodometer1 >= 0) ? $totalodometer1 : 0;?> km</b>
		<br /><?php echo $this->lang->line("lodometer"); ?> <?php echo $this->lang->line("luntil1"); ?> <?php echo date("d/m/Y H:i:s", $this->period2); ?> <b><? echo ($totalodometer >= 0) ? number_format($totalodometer, 0, ".", ",") : 0;?> km</b>
		<?php } ?>
		<?php if ($isgtp) { ?>
		<!--<h3>Workhour: </h3>-->
		<?php } ?>
		<br />
	<?php } ?>
		<table width="100%" cellpadding="3" class="tablelist">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th width="15%" colspan="2"><?=$this->lang->line("ldatetime"); ?></th>
					<th><?=$this->lang->line("lposition"); ?></th>
					<th width="10%"><?=$this->lang->line("lcoordinate"); ?></th>
					<?php if (($id == "overspeed") || ($id == "history")) { ?>
					<?php if ($id == "history") { ?>
					<th width="8%"><?=$this->lang->line("lstatus"); ?></th>
					<?php } ?>					
					<th width="8%"><?=$this->lang->line("lspeed"); ?></th>
					<?php } else if ($id == "parkingtime") { ?>
					<th width="8%"><?=$this->lang->line("lparking_time"); ?></th>
					<?php } ?>
					<th width="18px;">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php for($i=0; $i < count($data); $i++) { ?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><?=$i+1+$offset?></td>
					<td><?=$data[$i]->gps_date_fmt;?></td>
					<td><?=$data[$i]->gps_time_fmt;?></td>
					<td><?=$data[$i]->georeverse->display_name;?></td>
					<td><?=$data[$i]->gps_latitude_real_fmt;?> <?=$data[$i]->gps_longitude_real_fmt;?></td>
					<?php if (($id == "overspeed") || ($id == "history")) { ?>
					<?php if ($id == "history") { ?>
					<td style="text-align: center"><?php echo ($data[$i]->gps_status == "V") ? "NOT OK" : "OK"; ?></td>
					<?php } ?>
					<td style="text-align: right"><?=$data[$i]->gps_speed_fmt;?> <?=$this->lang->line("lkph"); ?></td>
					<?php } else if ($id == "parkingtime") { ?>
					<td style="text-align: right"><?php echo isset($data[$i]->parkingtime_fmt) ? $data[$i]->parkingtime_fmt : "";?></td>
					<?php } ?>
					<td><a href="<?=base_url(); ?>map/history/<?=$gps_name?>/<?=$gps_host?>/<?=$data[$i]->gps_id;?>"><img src="<?=base_url();?>assets/images/zoomin.gif" border="0"></a></td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="7"><?=$paging;?></td>
				</tr>
			</tfoot>
		</table>

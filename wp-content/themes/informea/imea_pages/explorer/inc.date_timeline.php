<form action="#">
	<fieldset>
		<label for="valueAA">From:</label>
		<?php
			$months = array();
			 
			foreach($sui->ui_get_months() as $idx => $mon) {
				$months[] = $mon;
			}
			
			foreach($sui->ui_compute_years() as $year) {
				$years[] = $year;
			}
		?>
		
		<select name="valueAA" id="valueAA">
			<?php
				foreach($years as $year){
			?>
			<optgroup label="<?php echo $year; ?>">
				<?php
					foreach($months as $month){
				?>
				
				<option value="<?php echo $month . '/' . $year; ?>">
					<?php echo $month . ' / ' . $year; ?>
				</option>
				
				<?php
					}
				?>
			</optgroup>
			<?php
				}
			?>
		</select>
	
		<label for="valueBB">To:</label>
		<select name="valueBB" id="valueBB">
		<?php
			foreach($years as $year){
		?>
		<optgroup label="<?php echo $year; ?>">
			<?php
				foreach($months as $month){
			?>
			
			<option value="<?php echo $month . '/' . $year; ?>">
				<?php echo $month . ' / ' . $year; ?>
			</option>
			
			<?php
				}
			?>
		</optgroup>
		<?php
			}
		?>
		</select>
	</fieldset>
</form>

<?php $select_options = array('empty' => false, 'multiple' => 'multiple', 'style' => 'width:300px', 'size' => 5); ?>

<form method="post" name="form" action="">
	<table class="border_plomo tb_base">
		<tr>
			<td align="right">
				<?php echo __('Fecha desde'); ?>
			</td>
			<td align="left">
				<?php echo $this->Html->PrintCalendar('start_date', $this->data['start_date']); ?>
			</td>
		</tr>
		<tr>
			<td align="right">
				<?php echo __('Fecha hasta'); ?>
			</td>
			<td align="left">
				<?php echo $this->Html->PrintCalendar('end_date', $this->data['end_date']); ?>
			</td>
		</tr>
		<tr>
			<td align="right">
			 <?php echo __('Clientes'); ?>
			</td>
			<td align="left">
				<?php echo $this->Form->select('clients[]', $clients, $this->data['clients'], $select_options); ?>
			</td>
		</tr>
		<tr>
			<td align="right">
				<?php echo __('Grupos Clientes'); ?>
			</td>
			<td align="left">
				<?php echo $this->Form->select('client_group[]', $client_group, $this->data['client_group'], $select_options); ?>
			</td>
		</tr>
		<tr>
			<td align="right">
				<?php echo __('Forma Tarificación'); ?>
			</td>
			<td align="left">
				<?php echo $this->Form->select('billing_strategy[]', $billing_strategy, $this->data['billing_strategy'], $select_options); ?>
			</td>
		</tr>
			<tr>
			<td align="right">
				<?php echo __('Facturado en'); ?>
			</td>
			<td align="left">
				<?php echo $this->Form->select('invoiced[]', $currency, $this->data['invoiced'], $select_options); ?>
			</td>
		</tr>
			<tr>
			<td align="right">
				<?php echo __('Mostrar valores en:'); ?>
			</td>
			<td align="left">
				<?php echo $this->Form->select('display_currency', $currency, ($this->data['display_currency'] ? $this->data['display_currency'] : $base_currency), array('empty' => false)); ?>
			</td>
		</tr>
		<tr>
			<td align="right">
				<?php echo __('Comparar seg&uacute;n:'); ?>
			</td>
			<td align="left">
				<?php echo $this->Form->select('rate', $rate, $this->data['rate'], array('empty' => false)); ?>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4" align="center">
				<?php echo $this->Form->submit(__('Generar planilla')); ?>
			</td>
		</tr>
	</table>
</form>

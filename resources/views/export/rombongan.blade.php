<table>
	<tr>
		<th>TANGGAL</th>
		<th>CABANG</th>
		<th>NAMA KENDARAAN</th>
		<th>BIRO</th>
		<th>B</th>
		<th>M</th>
		<th>T</th>
		<th>WELCOME</th>
		<th>TOTAL BELANJA</th>
		<th>TOTAL BELANJA BULAT</th>
		<th>FEE</th>
		<th>MARKETING</th>
		<th>KETERANGAN</th>
	</tr>
	@foreach($data as $value)
	<?php  
		preg_match_all('/(\d+)([BMT])/', $value->nama, $matches, PREG_SET_ORDER);
		$b = 0;
		$m = 0;
		$t = 0;
		foreach ($matches as $match) {
	        $value_car = (int)$match[1];
	        $type = $match[2];

	        if ($type === 'B') {
	            $b = $value_car;
	        } elseif ($type === 'M') {
	            $m = $value_car;
	        } elseif ($type === 'T') {
	            $t = $value_car;
	        }
	    }
	?>
	<tr>
		<td>{{ $value->created_at }}</td>
		<td>{{ $value->invoice->user->cabang->nama }}</td>
		<td></td>
		<td></td>
		<td>{{ $b }}</td>
		<td>{{ $m }}</td>
		<td>{{ $t }}</td>
		<td></td>
		<td>{{ $value->total_belanja }}</td>
		<td>{{ $value->total_belanja2 }}</td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	@endforeach
</table>
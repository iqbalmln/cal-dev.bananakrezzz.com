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
		// 1. Bigbus 100000
		// 2. Medium 80000
		// 3. Travel 50000

		// jumlahKendaraan_namaKendaraan_namaBiro_asalKota
		// 1B_Pesona_(Biro)_Jogja

		preg_match_all('/(\d+)([BMT])/', $value->nama, $matches, PREG_SET_ORDER);
		$b = 0;
		$m = 0;
		$t = 0;
		$nama_kendaraan = explode('_', $value->nama)[1];
		$biro = explode('_', $value->nama)[2];
		$wellcome = 0;
		foreach ($matches as $match) {
	        $value_car = (int)$match[1];
	        $type = $match[2];

	        if ($type === 'B') {
	            $b = $value_car;
	            $wellcome += $value_car*100000;
	        } elseif ($type === 'M') {
	            $m = $value_car;
	            $wellcome += $value_car*80000;
	        } elseif ($type === 'T') {
	            $t = $value_car;
	            $wellcome += $value_car*50000;
	        }
	    }

	?>
	<tr>
		<td>{{ $value->created_at }}</td>
		<td>{{ $value->invoice->user->cabang->nama }}</td>
		<td>{{ $nama_kendaraan }}</td>
		<td>{{ $biro }}</td>
		<td>{{ $b }}</td>
		<td>{{ $m }}</td>
		<td>{{ $t }}</td>
		<td>{{ $wellcome }}</td>
		<td>{{ str_replace('.','',$value->total_belanja) }}</td>
		<td>{{ str_replace('.','',$value->total_belanja2) }}</td>
		<td>{{ str_replace('.','',$value->fee) }}</td>
		<td></td>
		<td></td>
	</tr>
	@endforeach
</table>
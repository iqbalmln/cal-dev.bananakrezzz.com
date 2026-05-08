<table>
	<tr>
		<th>TANGGAL</th>
		<th>ROMBONGAN</th>
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

		// preg_match_all('/(\d+)([BMT])/', $value->nama, $matches, PREG_SET_ORDER);
		// $b = 0;
		// $m = 0;
		// $t = 0;
		// $nama_kendaraan = explode('_', $value->nama)[1];
		// $biro = explode('_', $value->nama)[2];
		// $wellcome = 0;
		// foreach ($matches as $match) {
	    //     $value_car = (int)$match[1];
	    //     $type = $match[2];

	    //     if ($type === 'B') {
	    //         $b = $value_car;
	    //         $wellcome += $value_car*100000;
	    //     } elseif ($type === 'M') {
	    //         $m = $value_car;
	    //         $wellcome += $value_car*80000;
	    //     } elseif ($type === 'T') {
	    //         $t = $value_car;
	    //         $wellcome += $value_car*50000;
	    //     }
	    // }


	    $result = [
	        'B' => 0,
	        'M' => 0,
	        'T' => 0,
	        'poBus' => '',
	        'biro' => '',
	    ];

	    if (preg_match('/(\d+)B/', $value->nama, $matchB)) {
	        $result['B'] = (int) $matchB[1];
	    }

	    if (preg_match('/(\d+)M/', $value->nama, $matchM)) {
	        $result['M'] = (int) $matchM[1];
	    }

	    if (preg_match('/(\d+)T/', $value->nama, $matchM)) {
	        $result['T'] = (int) $matchM[1];
	    }

	    preg_match_all('/\((.*?)\)/', $value->nama, $matches);
	    $brackets = $matches[1];

	        $result['poBus'] = count($brackets) > 0 ? trim($brackets[0]) : '';
	        $result['biro'] = count($brackets) > 2 ? trim($brackets[2]) : '';

		$wellcome = 0;
        if ($result['B'] != 0) {
            $wellcome += $result['B']*100000;
        } 
        if ($result['M'] != 0) {
            $wellcome += $result['M']*80000;
        } 
        if ($result['T'] != 0) {
            $wellcome += $result['T']*50000;
        }


	?>
	
	<tr>
		<td>{{ $value->created_at }}</td>
		<td>{{ $value->nama }}</td>
		<td>{{ $value->invoice->user->cabang->nama }}</td>
		<td>{{ $result['poBus'] }}</td>
		<td>{{ $result['biro'] }}</td>
		<td>{{ $result['B'] }}</td>
		<td>{{ $result['M'] }}</td>
		<td>{{ $result['T'] }}</td>
		<td>{{ $wellcome }}</td>
		<td>{{ str_replace('.','',$value->total_belanja) }}</td>
		<td>{{ str_replace('.','',$value->total_belanja2) }}</td>
		<td>{{ str_replace('.','',$value->fee) }}</td>
		<td></td>
		<td></td>
	</tr>
	@endforeach
</table>
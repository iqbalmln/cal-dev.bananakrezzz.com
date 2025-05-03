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
	<tr>
		<td>{{ $value->created_at }}</td>
		<td>{{ $value->invoice->user->cabang->nama }}</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td>{{ $value->total_belanja }}</td>
		<td>{{ $value->total_belanja2 }}</td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	@endforeach
</table>
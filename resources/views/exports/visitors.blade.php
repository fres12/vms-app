<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>NIK</th>
            <th>Company</th>
            <th>Phone</th>
            @if($isMasterAdmin)
                <th>Department</th>
            @endif
            <th>Visit Purpose</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Equipment</th>
            <th>Brand</th>
            <th>Submit Date</th>
            <th>Status</th>
            <th>ID Card Photo</th>
            <th>Self Photo</th>
            <th>Barcode</th>
            <th>Approved Date</th>
            <th>Ticket Number</th>
            <th>Remark</th>
        </tr>
    </thead>
    <tbody>
        @foreach($visitors as $i => $visitor)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $visitor->fullname }}</td>
                <td>{{ $visitor->email }}</td>
                <td>{{ (string) $visitor->nik }}</td>
                <td>{{ $visitor->company }}</td>
                <td>{{ $visitor->phone }}</td>
                @if($isMasterAdmin)
                    <td>{{ $visitor->department_name }}</td>
                @endif
                <td>{{ $visitor->visit_purpose }}</td>
                <td>{{ \Carbon\Carbon::parse($visitor->startdate)->format('d-m-Y H:i') }}</td>
                <td>{{ \Carbon\Carbon::parse($visitor->enddate)->format('d-m-Y H:i') }}</td>
                <td>{{ $visitor->equipment_type }}</td>
                <td>{{ $visitor->brand }}</td>
                <td>{{ \Carbon\Carbon::parse($visitor->submit_date)->format('d-m-Y H:i') }}</td>
                <td>{{ $visitor->status }}</td>
                <td>
                    @if($visitor->idcardphoto)
                        =HYPERLINK("{{ $visitor->id_card_url }}", "View ID Card")
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($visitor->selfphoto)
                        =HYPERLINK("{{ $visitor->self_photo_url }}", "View Self Photo")
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($visitor->barcode_url)
                        =HYPERLINK("{{ $visitor->barcode_url }}", "View QR")
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($visitor->approved_date)
                        {{ \Carbon\Carbon::parse($visitor->approved_date)->format('d-m-Y H:i') }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $visitor->ticket_number }}</td>
                <td>{{ $visitor->rejected_reason ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
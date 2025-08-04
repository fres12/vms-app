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
            <th>Equipment Type</th>
            <th>Brand</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Submit Date</th>
            <th>ID Card Photo</th>
            <th>Self Photo</th>
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
                <td>{{ $visitor->equipment_type }}</td>
                <td>{{ $visitor->brand }}</td>
                <td>{{ \Carbon\Carbon::parse($visitor->startdate)->format('d-m-Y H:i') }}</td>
                <td>{{ \Carbon\Carbon::parse($visitor->enddate)->format('d-m-Y H:i') }}</td>
                <td>{{ $visitor->status }}</td>
                <td>{{ \Carbon\Carbon::parse($visitor->submit_date)->format('d-m-Y H:i') }}</td>
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
            </tr>
        @endforeach
    </tbody>
</table>
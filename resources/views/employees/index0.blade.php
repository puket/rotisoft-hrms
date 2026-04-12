<!DOCTYPE html>
<html>
<body>
    <h1>รายชื่อพนักงานทั้งหมด</h1>
    <ul>
        @foreach($employees as $emp)
            <li>
                <a href="/employees/{{ $emp->id }}">
                    {{ $emp->name }}
                </a> 
                - {{ $emp->position }}
            </li>
        @endforeach
    </ul>
</body>
</html>
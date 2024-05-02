@extends('line.liff.template')

@section('content')
<script>
    const res = liffInit().then(function (res) {
        console.debug(res);
    });
</script>
@endsection

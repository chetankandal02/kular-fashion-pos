@extends('layouts.app')
@section('content')
    <pos-page></pos-page>
    @include('partials.sales-list-modal')
   
    <x-include-plugins :plugins="['dataTable', 'datePicker', 'chosen']"></x-include-plugins>
@endsection

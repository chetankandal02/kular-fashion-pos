@extends('layouts.app')
@section('content')
    <pos-page></pos-page>
    <sales-list-modal></sales-list-modal>
   
    <x-include-plugins :plugins="['dataTable', 'datePicker', 'chosen', 'select2']"></x-include-plugins>
@endsection

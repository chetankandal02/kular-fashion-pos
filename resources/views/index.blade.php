@extends('layouts.app')
@section('content')
    <pos-page></pos-page>
    
    <div class="modal fade" id="salesListModal" tabindex="-1" aria-labelledby="salesListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesListModalLabel">Sales List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Sales Person</th>
                            <th>Items</th>
                            <th>Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>Chetan</td>
                            <td>2</td>
                            <td>£249</td>
                          </tr>
                          <tr>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>Pummy</td>
                            <td>5</td>
                            <td>£499</td>
                          </tr>
                          <tr>
                            <td>3</td>
                            <td>Mark Johnson</td>
                            <td>Chetan</td>
                            <td>6</td>
                            <td>£549</td>
                          </tr>
                        </tbody>
                      </table>
                </div>
            </div>
        </div>
    </div>

    <x-include-plugins :plugins="['dataTable', 'datePicker']"></x-include-plugins>
@endsection

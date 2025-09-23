<script src="{{ asset('js/unsaved.js') }}"></script>

@extends('layouts.admin')
@section('page-title')
    {{ __('Journal Entry Create') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Double Entry') }}</li>
    <li class="breadcrumb-item">{{ __('Journal Entry') }}</li>
@endsection

@push('script-page')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        var selector = "body";
        if ($(selector + " .repeater").length) {
            var $repeater = $(selector + ' .repeater').repeater({
                initEmpty: false,
                defaultValues: {
                    'status': 1
                },
                show: function() {
                    $(this).slideDown();
                    var file_uploads = $(this).find('input.multi');
                    if (file_uploads.length) {
                        $(this).find('input.multi').MultiFile({
                            max: 3,
                            accept: 'png|jpg|jpeg',
                            max_size: 2048
                        });
                    }
                    if ($('.select2').length) {
                        $('.select2').select2();
                    }
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                        $(this).remove();

                        var inputs = $(".debit");
                        var totalDebit = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            totalDebit = parseFloat(totalDebit) + parseFloat($(inputs[i]).val());
                        }
                        $('.totalDebit').html(totalDebit.toFixed(2));

                        var inputs = $(".credit");
                        var totalCredit = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            totalCredit = parseFloat(totalCredit) + parseFloat($(inputs[i]).val());
                        }
                        $('.totalCredit').html(totalCredit.toFixed(2));

                    }
                },
                ready: function(setIndexes) {
                },
                isFirstItemUndeletable: true
            });
            var value = $(selector + " .repeater").attr('data-value');

            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
                for (var i = 0; i < value.length; i++) {
                    var tr = $('#sortable-table .id[value="' + value[i].id + '"]').parent();
                    tr.find('.item').val(value[i].product_id);
                    changeItem(tr.find('.item'));
                }
            }

        }

        $(document).on('keyup', '.debit', function() {
            var el = $(this).parent().parent().parent().parent();
            var debit = $(this).val();
            var credit = 0;
            el.find('.credit').val(credit);
            el.find('.amount').html(debit);

            var inputs = $(".debit");
            var totalDebit = 0;
            for (var i = 0; i < inputs.length; i++) {
                totalDebit = parseFloat(totalDebit) + parseFloat($(inputs[i]).val());
            }
            $('.totalDebit').html(totalDebit.toFixed(2));

            el.find('.credit').attr("disabled", true);
            if (debit == '') {
                el.find('.credit').attr("disabled", false);
            }
        })

        $(document).on('keyup', '.credit', function() {
            var el = $(this).parent().parent().parent().parent();
            var credit = $(this).val();
            var debit = 0;
            el.find('.debit').val(debit);
            el.find('.amount').html(credit);

            var inputs = $(".credit");
            var totalCredit = 0;
            for (var i = 0; i < inputs.length; i++) {
                totalCredit = parseFloat(totalCredit) + parseFloat($(inputs[i]).val());
            }
            $('.totalCredit').html(totalCredit.toFixed(2));

            el.find('.debit').attr("disabled", true);
            if (credit == '') {
                el.find('.debit').attr("disabled", false);
            }
        })
    </script>
@endpush
@php
    $plan = \App\Models\Utility::getChatGPTSettings();
@endphp
@section('content')
    {{ Form::open(['url' => 'journal-entry', 'class'=>'w-100 needs-validation','novalidate']) }}
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                <div class="h-1 w-full" style="background:#007C38;"></div>
            <div class="card-body">
                    <div class="row">
                        @if ($plan->enable_chatgpt == 'on')
                            <div>
                                <a href="#" data-size="md" data-ajax-popup-over="true"
                                    data-url="{{ route('generate', ['journal account']) }}" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="{{ __('Generate') }}"
                                    data-title="{{ __('Generate content with AI') }}"
                                    class="btn btn-primary btn-sm float-end">
                                    <i class="fas fa-robot"></i>
                                    {{ __('Generate with AI') }}
                                </a>
                            </div>
                        @endif
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                {{ Form::label('journal_number', __('Journal Number'), ['class' => 'form-label']) }}
                                <div class="form-icon-user">
                                    <input type="text" class="form-control"
                                        value="{{ \Auth::user()->journalNumberFormat($journalId) }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                {{ Form::label('date', __('Transaction Date'), ['class' => 'form-label']) }}
                                <div class="form-icon-user">
                                    {{ Form::date('date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                {{ Form::label('reference', __('Reference'), ['class' => 'form-label']) }}
                                <div class="form-icon-user">
                                    <span><i class="ti ti-joint"></i></span>
                                    {{ Form::text('reference', '', ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-8">
                            <div class="form-group">
                                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                                {{ Form::textarea('description', '', ['class' => 'form-control', 'rows' => '3']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                <div class="h-1 w-full" style="background:#007C38;"></div>
            <div class="item-section py-4">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                            <a href="#" data-repeater-create="" class="btn btn-primary mr-2" data-toggle="modal"
                                data-target="#add-bank">
                                <i class="ti ti-plus"></i> {{ __('Add Accounts') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0" data-repeater-list="accounts" id="sortable-table">

                            <tbody class="ui-sortable" data-repeater-item>
                                <tr>
                                  <td colspan="6">
                                    <div class="border rounded p-3 mb-3 bg-light-subtle">
                              
                                      {{-- Account --}}
                                      <div class="mb-3">
                                        {{ Form::label('account', __('Account'), ['class' => 'form-label fw-semibold']) }}
                                        {{ Form::select('account', $accounts, '', [
                                            'class' => 'form-control select',
                                            'required' => 'required'
                                        ]) }}
                                      </div>
                              
                                      {{-- Debit / Credit --}}
                                      <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                          {{ Form::label('debit', __('Debit'), ['class' => 'form-label fw-semibold']) }}
                                          <div class="input-group">
                                            {{ Form::text('debit', '', [
                                                'class' => 'form-control debit',
                                                'required' => 'required',
                                                'placeholder' => __('Debit')
                                            ]) }}
                                            <span class="input-group-text bg-white">{{ \Auth::user()->currencySymbol() }}</span>
                                          </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                          {{ Form::label('credit', __('Credit'), ['class' => 'form-label fw-semibold']) }}
                                          <div class="input-group">
                                            {{ Form::text('credit', '', [
                                                'class' => 'form-control credit',
                                                'required' => 'required',
                                                'placeholder' => __('Credit')
                                            ]) }}
                                            <span class="input-group-text bg-white">{{ \Auth::user()->currencySymbol() }}</span>
                                          </div>
                                        </div>
                                      </div>
                              
                                      {{-- Description --}}
                                      <div class="mt-3 mb-2">
                                        {{ Form::label('description', __('Description'), ['class' => 'form-label fw-semibold']) }}
                                        {{ Form::text('description', '', [
                                            'class' => 'form-control',
                                            'placeholder' => __('Description')
                                        ]) }}
                                      </div>
                              
                                      {{-- Line amount + delete --}}
                                      <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                          <strong>{{ __('Amount') }}:</strong>
                                          <span class="amount fw-bold">0.00</span>
                                        </div>
                                        <div data-repeater-delete>
                                          <a href="#" class="btn btn-sm btn-outline-danger">
                                            <i class="ti ti-trash"></i> {{ __('Remove') }}
                                          </a>
                                        </div>
                                      </div>
                              
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                              
                            <tfoot>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td></td>
                                    <td class="text-end"><strong>{{ __('Total Credit') }}
                                            ({{ \Auth::user()->currencySymbol() }})</strong></td>
                                    <td class="text-end totalCredit">0.00</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td class="text-end"><strong>{{ __('Total Debit') }}
                                            ({{ \Auth::user()->currencySymbol() }})</strong></td>
                                    <td class="text-end totalDebit">0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" onclick="location.href = '{{ route('journal-entry.index') }}';"
            class="btn btn-light">
        <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
    </div>
    {{ Form::close() }}
@endsection

@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12">
        {{-- Reason --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Reason For Refund Request') }}</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-lg-2 col-from-label"><b>{{ translate('Reason') }}:</b></label>
                    <div class="col-lg-8">
                        <p class="bord-all pad-all mb-0">{{ $refund->reason }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attachments --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Attachments') }}</h5>
            </div>
            <div class="card-body">
                @php
                    $attachmentIds = collect(explode(',', (string) ($refund->attachments ?? '')))
                        ->map(fn($v) => trim($v))
                        ->filter(fn($v) => $v !== '')
                        ->values();
                @endphp

                @if ($attachmentIds->isEmpty())
                    <p class="text-muted mb-0">{{ translate('No attachments uploaded.') }}</p>
                @else
                    <div class="row">
                        @foreach ($attachmentIds as $id)
                            @php
                                $upload = \App\Models\Upload::find($id);
                                $url    = function_exists('uploaded_asset') ? uploaded_asset($id) : null;
                                $name   = optional($upload)->file_original_name ?? ('Image #' . $id);
                            @endphp
                            @if ($url)
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                                    <a href="{{ $url }}" target="_blank" class="d-block border rounded overflow-hidden">
                                        <img
                                            src="{{ $url }}"
                                            alt="{{ e($name) }}"
                                            class="img-fluid"
                                            style="width:100%;height:140px;object-fit:cover;"
                                        >
                                    </a>
                                    <div class="small text-truncate mt-1" title="{{ e($name) }}">{{ $name }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection

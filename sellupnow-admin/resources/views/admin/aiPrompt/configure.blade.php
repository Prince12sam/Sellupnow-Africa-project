@extends('layouts.app')

@section('header-title', __('OpenAI API Configuration'))

@section('content')
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-xl-8 col-lg-9 m-auto">
                <form action="{{ route('admin.aiPrompt.configure.update') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header py-3">
                            <h4 class="m-0">{{ __('OpenAI API Configuration') }}</h4>
                        </div>
                        <div class="card-body pb-4">
                            <div class="mb-3">
                                <x-input type="text" name="api_key" label="API KEY" required placeholder="ADD API KEY" :value="config('openai.api_key')"/>
                                <small class="text-muted">For <strong>OpenRouter</strong> keys (<code>sk-or-v1-…</code>), also set the Base URL below.</small>
                            </div>

                            <div class="mb-3">
                                <x-input type="text" name="organization" label="OPENAI ORGANIZATION" placeholder="org-xxxx (optional — leave blank if you only have one org)" :value="config('openai.organization')"/>
                            </div>

                            <div class="mb-4">
                                <x-input type="text" name="base_url" label="BASE URL (OpenRouter / custom endpoint)" placeholder="https://openrouter.ai/api/v1 — leave blank for native OpenAI" :value="config('openai.base_uri')"/>
                                <small class="text-muted">
                                    Native OpenAI: leave blank.<br>
                                    OpenRouter: <code>https://openrouter.ai/api/v1</code>
                                </small>
                            </div>
                        </div>
                        @hasPermission('admin.aiPrompt.configure.update')
                        <div class="card-footer py-3 ">
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary py-2">{{ __('Save And Update') }}</button>
                            </div>
                        </div>
                        @endhasPermission
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

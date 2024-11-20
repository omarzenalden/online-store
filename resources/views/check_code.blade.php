@extends('layouts.app')

@section('title', 'Enter Reset Code')

@section('content')
    <div class="container">
        <h2>Enter Reset Code</h2>
        <form method="POST" action="{{ route('password.check_code') }}" onsubmit="disableButton()">
            @csrf
            <div class="form-group">
                <label for="code">Reset Code</label>
                <input type="text" name="code" class="form-control" id="code" placeholder="Enter the 6-digit code" required>
            </div>
            <button type="submit" class="btn btn-primary" id="submitBtn">Verify Code</button>
        </form>

        <a href="{{ route('password.resend') }}" class="btn btn-link mt-3">Resend Code</a> <!-- Add this link -->

        <script>
            function disableButton() {
                document.getElementById('submitBtn').disabled = true;
            }
        </script>

        @if (session('status'))
            <div class="alert alert-success mt-3">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection

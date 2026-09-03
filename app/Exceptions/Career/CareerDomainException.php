<?php

namespace App\Exceptions\Career;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CareerDomainException extends Exception
{
    public function __construct(
        string $message,
        protected int $status = 422,
        protected array $errors = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $status, $previous);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function render(Request $request): JsonResponse|Response
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
                'errors' => $this->errors,
            ], $this->status);
        }

        if ($request->isMethod('GET')) {
            return response()->view('frontend.careers.token-error', [
                'message' => $this->getMessage(),
            ], $this->status);
        }

        return back()
            ->withInput()
            ->withErrors($this->errors ?: ['_career' => $this->getMessage()])
            ->with('error', $this->getMessage());
    }
}

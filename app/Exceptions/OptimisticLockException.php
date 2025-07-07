<?php

namespace App\Exceptions;

use Exception;

class OptimisticLockException extends Exception
{
    /**
     * The model that was being updated.
     */
    protected ?string $modelClass;

    /**
     * The model ID that was being updated.
     */
    protected ?int $modelId;

    /**
     * Create a new exception instance.
     */
    public function __construct(
        string $message = 'Resource was modified by another process',
        ?string $modelClass = null,
        ?int $modelId = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->modelClass = $modelClass;
        $this->modelId = $modelId;
    }

    /**
     * Get the model class.
     */
    public function getModelClass(): ?string
    {
        return $this->modelClass;
    }

    /**
     * Get the model ID.
     */
    public function getModelId(): ?int
    {
        return $this->modelId;
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Concurrent modification detected',
                'message' => $this->getMessage(),
                'model_class' => $this->modelClass,
                'model_id' => $this->modelId,
                'suggestion' => 'Please refresh and try again',
            ], 409); // Conflict status code
        }

        return back()->withErrors([
            'general' => 'The resource was modified by another process. Please refresh and try again.'
        ])->withInput();
    }
}
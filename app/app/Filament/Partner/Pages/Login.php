<?php

namespace App\Filament\Partner\Pages;

use App\Enums\UserTypeEnum;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Login extends \Filament\Pages\Auth\Login
{
    public function __invoke()
    {
        if(Auth::guard('admin')->check()) {
            $path = Filament::getPanel('admin')->getPath();
            return redirect()->to(url($path));
        }

        return parent::__invoke();
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        $user = User::select('type')->byEmail($data['email'])->first();

        if($user && $user->type === UserTypeEnum::ADMIN) {
            Filament::setCurrentPanel(Filament::getPanel('admin'));
        }

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        /** @var \App\Models\User $user */
        $user = Filament::auth()->user();

        if($user->type !== UserTypeEnum::ADMIN && !$user->is_active) {
            Filament::auth()->logout();
            throw ValidationException::withMessages([
                'data.email' => 'Usuário não aprovado no sistema',
            ]);
        }

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}

<div>
    
    <x-jet-form-section submit="createApiToken">
        <x-slot name="title">
            {{ __('Create API Token') }}
        </x-slot>

        <x-slot name="description">
            {{ __('API tokens allow third-party services to authenticate with our application on your behalf.') }}
        </x-slot>

        <x-slot name="form">
            
            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="name" value="{{ __('Token Name') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="createApiTokenForm.name" autofocus />
                <x-jet-input-error for="name" class="mt-2" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasPermissions())
                <div class="col-span-6">
                    <x-jet-label for="permissions" value="{{ __('Permissions') }}" />

                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach (Laravel\Jetstream\Jetstream::$permissions as $permission)
                            <label class="flex items-center">
                                <x-jet-checkbox wire:model.defer="createApiTokenForm.permissions" :value="$permission"/>
                                <span class="ml-2 text-sm text-gray-600">{{ $permission }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="created">
                {{ __('Created.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Create') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    @if ($this->user->tokens->isNotEmpty())
        <x-jet-section-border />

        <div class="mt-10 sm:mt-0">
            <x-jet-action-section>
                <x-slot name="title">
                    {{ __('Manage API Tokens') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('You may delete any of your existing tokens if they are no longer needed.') }}
                </x-slot>

                <x-slot name="content">
                    <div class="space-y-6">
                        @foreach ($this->user->tokens->sortBy('name') as $token)
                            <div class="flex items-center justify-between">
                                <div>
                                    {{ $token->name }}
                                </div>

                                <div class="flex items-center">
                                    @if ($token->last_used_at)
                                        <div class="text-sm text-gray-400">
                                            {{ __('Last used') }} {{ $token->last_used_at->diffForHumans() }}
                                        </div>
                                    @endif

                                    @if (Laravel\Jetstream\Jetstream::hasPermissions())
                                        <button class="cursor-pointer ml-6 text-sm text-gray-400 underline" wire:click="manageApiTokenPermissions({{ $token->id }})">
                                            {{ __('Permissions') }}
                                        </button>
                                    @endif

                                    <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="confirmApiTokenDeletion({{ $token->id }})">
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-slot>
            </x-jet-action-section>
        </div>
    @endif

    <x-jet-dialog-modal wire:model="displayingToken">
        <div style="border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.12);">
            <div style="background: linear-gradient(90deg, #198754 0%, #20c997 100%); padding: 2rem 2rem 1.25rem;">
                <h4 style="margin: 0; font-weight: 600; font-size: 1.5rem; color: white;">{{ __('API Token') }}</h4>
                <p style="margin: 0; opacity: 0.9; font-size: 0.875rem; color: white;">Please copy your new API token. For your security, it won't be shown again.</p>
            </div>
            <div style="padding: 2rem; background: white;">
                <x-jet-input x-ref="plaintextToken" type="text" readonly :value="$plainTextToken"
                    class="bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500 w-full"
                    autofocus autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                    @showing-token-modal.window="setTimeout(() => $refs.plaintextToken.select(), 250)"
                />
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                    <x-jet-secondary-button wire:click="$set('displayingToken', false)" wire:loading.attr="disabled" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1px solid #e0e7ff;">{{ __('Close') }}</x-jet-secondary-button>
                </div>
            </div>
        </div>
    </x-jet-dialog-modal>

    <x-jet-dialog-modal wire:model="managingApiTokenPermissions">
        <div style="border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.12);">
            <div style="background: linear-gradient(90deg, #198754 0%, #20c997 100%); padding: 2rem 2rem 1.25rem;">
                <h4 style="margin: 0; font-weight: 600; font-size: 1.5rem; color: white;">{{ __('API Token Permissions') }}</h4>
            </div>
            <div style="padding: 2rem; background: white;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach (Laravel\Jetstream\Jetstream::$permissions as $permission)
                        <label class="flex items-center" style="margin-bottom: 1rem;">
                            <x-jet-checkbox wire:model.defer="updateApiTokenForm.permissions" :value="$permission"/>
                            <span class="ml-2 text-sm text-gray-600">{{ $permission }}</span>
                        </label>
                    @endforeach
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                    <x-jet-secondary-button wire:click="$set('managingApiTokenPermissions', false)" wire:loading.attr="disabled" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1px solid #e0e7ff;">{{ __('Cancel') }}</x-jet-secondary-button>
                    <x-jet-button class="ml-2" wire:click="updateApiToken" wire:loading.attr="disabled" style="background: #198754; color: white; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500;">{{ __('Save') }}</x-jet-button>
                </div>
            </div>
        </div>
    </x-jet-dialog-modal>

    <x-jet-confirmation-modal wire:model="confirmingApiTokenDeletion">
        <div style="border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.12);">
            <div style="background: linear-gradient(90deg, #198754 0%, #20c997 100%); padding: 2rem 2rem 1.25rem;">
                <h4 style="margin: 0; font-weight: 600; font-size: 1.5rem; color: white;">{{ __('Delete API Token') }}</h4>
            </div>
            <div style="padding: 2rem; background: white;">
                <p style="font-size: 1rem; color: #374151;">{{ __('Are you sure you would like to delete this API token?') }}</p>
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                    <x-jet-secondary-button wire:click="$toggle('confirmingApiTokenDeletion')" wire:loading.attr="disabled" style="border-radius: 8px; padding: 0.75rem 1.5rem; border: 1px solid #e0e7ff;">{{ __('Cancel') }}</x-jet-secondary-button>
                    <x-jet-danger-button class="ml-2" wire:click="deleteApiToken" wire:loading.attr="disabled" style="background: #dc3545; color: white; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500;">{{ __('Delete') }}</x-jet-danger-button>
                </div>
            </div>
        </div>
    </x-jet-confirmation-modal>
</div>

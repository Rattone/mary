<?php

namespace Mary\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Radio extends Component
{
    public string $uuid;

    public function __construct(
        public ?string $label = null,
        public ?string $hint = null,
        public ?string $hintClass = 'label-text-alt text-base-content/50',
        public ?string $optionValue = 'id',
        public ?string $optionLabel = 'name',
        public ?string $optionHint = 'hint',
        public Collection|array $options = new Collection(),
        public ?bool $inline = false,

        // Validations
        public ?string $errorField = null,
        public ?string $errorClass = 'text-error label-text-alt p-1',
        public ?bool $omitError = false,
        public ?bool $firstErrorOnly = false,
    ) {
        $this->uuid = "mary" . md5(serialize($this));
    }

    public function modelName(): ?string
    {
        return $this->attributes->whereStartsWith('wire:model')->first();
    }

    public function errorFieldName(): ?string
    {
        return $this->errorField ?? $this->modelName();
    }

    public function render(): View|Closure|string
    {
        return <<<'HTML'
                <div>
                    @if($label)
                        <div class="pt-0 label label-text font-semibold">
                            <span>
                                {{ $label }}

                                @if($attributes->get('required'))
                                    <span class="text-error">*</span>
                                @endif
                            </span>
                        </div>
                    @endif

                    <div @class(["gap-4 grid", "!flex" => $inline])>
                        @foreach ($options as $option)
                            <label>
                                <div class="flex gap-2">
                                    <input
                                        type="radio"
                                        name="{{ $modelName() }}"
                                        value="{{ data_get($option, $optionValue) }}"
                                        @if(data_get($option, 'disabled')) disabled @endif
                                        {{ $attributes->whereStartsWith('wire:model') }}
                                        {{
                                            $attributes->class([
                                                "radio radio-sm",
                                            ])
                                        }}
                                    />
                                    <div>
                                        <div class="-mt-0.5 cursor-pointer font-medium">
                                            {{ data_get($option, $optionLabel) }}
                                        </div>
                                         <div class="{{ $hintClass }}" x-classes="label-text-alt text-base-content/50">
                                            {{ data_get($option, $optionHint) }}
                                         </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    {{-- ERROR --}}
                    @if(!$omitError && $errors->has($errorFieldName()))
                        @foreach($errors->get($errorFieldName()) as $message)
                            @foreach(Arr::wrap($message) as $line)
                                <div class="{{ $errorClass }}" x-classes="text-error label-text-alt p-1">{{ $line }}</div>
                                @break($firstErrorOnly)
                            @endforeach
                            @break($firstErrorOnly)
                        @endforeach
                    @endif
                </div>
            HTML;
    }
}

@props([ 'name' => null, 'id' => null, 'label' => null, 'value' => null, 'rows' => 3, 'placeholder' => null, 'help' => null, 'error' => null, 'required' => false, 'disabled' => false, ]) @php $textareaId = $id ?: ($name ?: 'ui-textarea-' . uniqid()); $hasError = $error || ($name && isset($errors) && $errors->has($name)); $errorMessage = $error ?: ($name && isset($errors) ? $errors->first($name) : null); $textValue = $value !== null ? $value : ($name ? old($name) : ''); $textareaClasses = [ 'ui-textarea', $hasError ? 'is-invalid' : '', ]; $textareaClasses = trim(implode(' ', array_filter($textareaClasses))); @endphp <div class="ui-form-group"> @if($label) <label for="{{ $textareaId }}" class="ui-label">{{ $label }} @if($required) <span class="ui-label-required">*</span> @endif </label> @endif <div class="ui-input-wrapper"> <textarea 
            id="{{ $textareaId }}"
            @if($name) name="{{ $name }}" @endif
            rows="{{ $rows }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => $textareaClasses]) }}
        >{{ $textValue ?: $slot }}</textarea> </div> @if($hasError) <span class="ui-error-text">{{ $errorMessage }}</span> @elseif($help) <span class="ui-help-text">{{ $help }}</span> @endif </div>
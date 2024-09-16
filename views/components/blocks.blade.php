@php
    $originalHash = blockhash()
@endphp

@foreach($blocks as $block)

    @php
        @list($component, $hash) = $block;
        blockhash($hash)
    @endphp
    @php
        /** @var \Illuminate\View\ComponentAttributeBag $attributes2 */
        $attributes2 = new \Illuminate\View\ComponentAttributeBag();
        $attributes2->setAttributes($__data);
        $attributes2 = $attributes2->whereDoesntStartWith("__");
    @endphp
    <x-dynamic-component :component="$component" :attributes="$attributes2"/>
    @php(blockhash(null))
@endforeach

@php(blockhash($originalHash))
<script lang="ts">
    let {
        class: className = '',
        ...rest
    }: {
        class?: string;
        [key: string]: unknown;
    } = $props();
</script>

<img
    src="/sic.png"
    alt="San Isidro College Logo"
    class={className}
    {...rest}
/>

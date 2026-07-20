<template>
  <footer
    class="absolute bottom-0 flex h-[60px] w-full items-center border-t border-line bg-surface"
  >
    <div class="mx-auto w-full max-w-7xl px-4 text-center sm:px-6 lg:px-8">
      <span class="font-mono text-[11px] tracking-[0.04em] text-muted">
        &copy; 2020 Bang
        <a
          href="https://github.com/elconejito/bang"
          class="ml-1 inline-flex align-middle text-muted transition-colors hover:text-brass-800"
          aria-label="Bang on GitHub"
        >
          <font-awesome-icon :icon="['fab', 'github-square']" />
        </a>
      </span>
    </div>
  </footer>
</template>

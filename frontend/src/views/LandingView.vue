
<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import {
  FolderHeart,
  TrendingUp,
  Sparkles,
  BookmarkCheck,
  Menu,
  X,
} from 'lucide-vue-next'

import hero from '@/assets/hero-crafts.jpg'

import Star from '@/components/doodles/Star.vue'
import Flower from '@/components/doodles/Flower.vue'
import Squiggle from '@/components/doodles/Squiggle.vue'
import Heart from '@/components/doodles/Heart.vue'
import YarnBall from '@/components/doodles/YarnBall.vue'
import Logo from '@/components/doodles/Logo.vue'

const menuOpen = ref(false)

const features = [
  {
    icon: FolderHeart,
    title: 'Keep Your Projects Organized',
    text: 'Every crochet blanket, scrapbook and clay charm lives in one tidy, cheerful space.',
    bg: 'bg-sky',
    rot: '-rotate-1',
  },
  {
    icon: TrendingUp,
    title: 'Track Every Little Progress',
    text: 'Slide your progress forward and watch your creations grow, stitch by stitch.',
    bg: 'bg-butter',
    rot: 'rotate-1',
  },
  {
    icon: Sparkles,
    title: 'Save Your Creative Inspiration',
    text: 'Pin a reference image to each project so you never forget the vision.',
    bg: 'bg-paper',
    rot: 'rotate-[-0.5deg]',
  },
  {
    icon: BookmarkCheck,
    title: 'Never Lose Your Tutorials',
    text: 'Keep that perfect YouTube video or blog pattern right next to your project.',
    bg: 'bg-cherry-soft',
    rot: 'rotate-[0.8deg]',
  },
]

const categories = [
  'crochet',
  'knitting',
  'scrapbooking',
  'painting',
  'embroidery',
  'beading',
  'clay',
  'sewing',
]

const steps = [
  {
    n: '01',
    title: 'Add Your Craft Project',
    text: 'Name it, pick a category, paste an inspiration image.',
  },
  {
    n: '02',
    title: 'Track Your Progress',
    text: 'Update your percentage and notes as you go.',
  },
  {
    n: '03',
    title: 'Celebrate Your Creation',
    text: 'Hit 100% and admire your growing handmade gallery.',
  },
]

function closeMenu() {
  menuOpen.value = false
}
</script>

<template>
  <div id="top" class="overflow-x-hidden">
    <!-- Navbar -->
    <header
      class="sticky top-0 z-30 border-b-2 border-cocoa bg-cream/95 backdrop-blur"
    >
      <div
        class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6"
      >
        <Logo />

        <nav class="hidden items-center gap-1 md:flex">
          <a href="#top" class="rounded-full px-3 py-2 font-medium hover:bg-butter">
            Home
          </a>

          <a href="#features" class="rounded-full px-3 py-2 font-medium hover:bg-butter">
            Features
          </a>

          <a href="#how" class="rounded-full px-3 py-2 font-medium hover:bg-butter">
            How It Works
          </a>

          <RouterLink
            to="/login"
            class="rounded-full px-3 py-2 font-medium hover:bg-butter"
          >
            Log In
          </RouterLink>
        </nav>

        <RouterLink
          to="/register"
          class="btn btn-primary hidden md:inline-flex"
        >
          Get Started
        </RouterLink>

        <button
          type="button"
          class="btn btn-ghost !px-3 md:hidden"
          aria-label="Menu"
          :aria-expanded="menuOpen"
          @click="menuOpen = !menuOpen"
        >
          <X v-if="menuOpen" />
          <Menu v-else />
        </button>
      </div>

      <!-- Mobile navigation -->
      <nav
        v-if="menuOpen"
        class="flex flex-col gap-1 border-t-2 border-cocoa p-3 md:hidden"
        @click="closeMenu"
      >
        <a href="#top" class="rounded-full px-3 py-2 font-medium hover:bg-butter">
          Home
        </a>

        <a href="#features" class="rounded-full px-3 py-2 font-medium hover:bg-butter">
          Features
        </a>

        <a href="#how" class="rounded-full px-3 py-2 font-medium hover:bg-butter">
          How It Works
        </a>

        <RouterLink
          to="/login"
          class="rounded-full px-3 py-2 font-medium hover:bg-butter"
        >
          Log In
        </RouterLink>

        <RouterLink to="/register" class="btn btn-primary mt-2">
          Get Started
        </RouterLink>
      </nav>
    </header>

    <!-- Hero -->
    <section class="paper-dots relative">
      <div
        class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-14 sm:px-6 lg:grid-cols-[1.1fr_1fr] lg:py-24"
      >
        <div class="relative animate-rise">
          <span class="label-sticker -rotate-2 bg-butter">
            ✿ your crafting journal
          </span>

          <h1
            class="mt-6 text-5xl font-bold leading-[0.95] sm:text-7xl lg:text-[5.5rem]"
          >
            Big ideas,
            <em class="font-medium text-sky-deep">little stitches,</em>
            <span class="relative inline-block">
              beautiful
              <Squiggle
                class="absolute -bottom-3 left-0 h-4 w-full text-cherry"
              />
            </span>
            creations.
          </h1>

          <p
            class="mt-8 max-w-lg text-lg leading-relaxed text-muted-foreground"
          >
            Your cozy corner to organize craft projects, track progress,
            and turn creative ideas into something handmade.
          </p>

          <div class="mt-8 flex flex-wrap gap-3">
            <RouterLink to="/register" class="btn btn-primary text-base">
              Start Creating
            </RouterLink>

            <a href="#features" class="btn btn-ghost text-base">
              Explore Features
            </a>
          </div>

          <Star
            class="absolute -right-2 top-0 hidden h-12 w-12 text-butter animate-floaty sm:block"
          />
        </div>

        <!-- Hero image -->
        <div class="relative mx-auto w-full max-w-lg">
          <div class="tape -top-3 left-10 z-10 -rotate-6"></div>
          <div class="tape -bottom-2 right-8 z-10 rotate-6"></div>

          <img
            :src="hero"
            alt="Flat lay of crochet squares, a scrapbook, watercolors and an embroidery hoop"
            width="1200"
            height="1408"
            class="aspect-[5/6] w-full rotate-2 rounded-[2rem] border-2 border-cocoa object-cover shadow-pop"
          />

          <div
            class="card-craft absolute -left-6 bottom-16 -rotate-6 px-4 py-3 shadow-pop-sm sm:-left-12"
          >
            <p class="font-fun text-xs text-muted-foreground">
              Granny square blanket
            </p>

            <div class="mt-1 flex items-center gap-2">
              <div
                class="h-2.5 w-24 overflow-hidden rounded-full border-2 border-cocoa"
              >
                <div class="h-full w-[62%] bg-sky"></div>
              </div>

              <span class="text-sm font-bold">62%</span>
            </div>
          </div>

          <span
            class="label-sticker absolute -right-3 top-12 rotate-6 bg-cherry text-cream shadow-pop-sm sm:-right-8"
          >
            handmade ♡
          </span>

          <Flower
            class="absolute -bottom-8 left-1/3 h-16 w-16 text-sky animate-floaty"
          />

          <YarnBall
            class="absolute -right-6 -bottom-6 h-16 w-16 text-cherry animate-floaty"
            style="animation-delay: 1.5s"
          />
        </div>
      </div>
    </section>

    <!-- Marquee band -->
    <div class="border-y-2 border-cocoa bg-cocoa py-3 text-cream">
      <p
        class="flex flex-wrap justify-center gap-x-6 gap-y-1 px-4 font-display text-lg italic sm:text-xl"
      >
        <span
          v-for="category in categories"
          :key="category"
          class="inline-flex items-center gap-6"
        >
          {{ category }}
          <span class="text-butter">✿</span>
        </span>
      </p>
    </div>

    <!-- Features -->
    <section
      id="features"
      class="mx-auto max-w-7xl scroll-mt-20 px-4 py-20 sm:px-6 lg:py-28"
    >
      <div class="max-w-2xl">
        <span class="label-sticker bg-sky">
          features
        </span>

        <h2 class="mt-4 text-4xl font-bold sm:text-6xl">
          Everything your craft basket
          <em class="font-medium">wishes</em> it had.
        </h2>
      </div>

      <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div
          v-for="(feature, index) in features"
          :key="feature.title"
          :class="[
            'card-craft flex flex-col gap-4 p-7 transition-all hover:rotate-0 hover:-translate-y-2 hover:shadow-pop',
            feature.bg,
            feature.rot,
            index % 2 ? 'lg:mt-10' : '',
          ]"
        >
          <span
            class="grid h-12 w-12 place-items-center rounded-2xl border-2 border-cocoa bg-paper"
          >
            <component :is="feature.icon" class="h-6 w-6" />
          </span>

          <h3 class="text-2xl font-semibold leading-tight">
            {{ feature.title }}
          </h3>

          <p class="leading-relaxed">
            {{ feature.text }}
          </p>
        </div>
      </div>
    </section>

    <!-- How It Works -->
    <section
      id="how"
      class="scroll-mt-16 border-y-2 border-cocoa bg-butter"
    >
      <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:py-28">
        <div class="flex flex-wrap items-end justify-between gap-4">
          <h2 class="text-4xl font-bold sm:text-6xl">
            How it works
          </h2>

          <p class="max-w-sm text-lg">
            Three little steps. No fuss, just making.
          </p>
        </div>

        <ol class="mt-14 grid gap-6 md:grid-cols-3">
          <li
            v-for="(step, index) in steps"
            :key="step.n"
            class="card-craft relative p-8"
          >
            <span
              class="font-display text-7xl font-bold italic text-sky-deep"
            >
              {{ step.n }}
            </span>

            <h3 class="mt-4 text-2xl font-semibold">
              {{ step.title }}
            </h3>

            <p class="mt-2 text-muted-foreground">
              {{ step.text }}
            </p>

            <Heart
              v-if="index === 2"
              class="absolute right-6 top-6 h-10 w-10 text-cherry animate-floaty"
            />
          </li>
        </ol>
      </div>
    </section>

    <!-- Call to Action -->
    <section
      class="mx-auto max-w-5xl px-4 py-20 text-center sm:px-6 lg:py-28"
    >
      <div class="relative">
        <Star
          class="absolute left-4 top-0 h-10 w-10 text-sky animate-floaty"
        />

        <Flower
          class="absolute bottom-0 right-6 h-12 w-12 text-cherry animate-floaty"
        />

        <h2
          class="mx-auto max-w-3xl text-5xl font-bold leading-tight sm:text-7xl"
        >
          Your next creative adventure starts here.
        </h2>

        <RouterLink
          to="/register"
          class="btn btn-dark mt-10 text-lg !px-8 !py-4"
        >
          Create Your Craft Space
        </RouterLink>
      </div>
    </section>

    <!-- Footer -->
    <footer class="border-t-2 border-cocoa bg-paper">
      <div
        class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-10 sm:px-6 md:flex-row md:items-center md:justify-between"
      >
        <div>
          <Logo />

          <p class="mt-2 text-sm text-muted-foreground">
            A cozy journal for everything handmade.
          </p>
        </div>

        <nav class="flex flex-wrap gap-4 text-sm font-medium">
          <a href="#top">Home</a>
          <a href="#features">Features</a>
          <a href="#how">How It Works</a>
          <RouterLink to="/login">Log In</RouterLink>
          <RouterLink to="/register">Get Started</RouterLink>
        </nav>

        <p class="text-sm text-muted-foreground">
          © 2026 Craftory
        </p>
      </div>
    </footer>
  </div>
</template>
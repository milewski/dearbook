<template>

    <Dialog>

        <DialogTrigger as-child>
            <slot/>
        </DialogTrigger>

        <DialogContent class="sm:max-w-4xl grid-rows-[auto_minmax(0,1fr)_auto] p-0 max-h-[100dvh] lg:max-h-[90dvh]">

            <DialogHeader class="p-12 pb-0">
                <DialogTitle>Manifesto</DialogTitle>
            </DialogHeader>

            <div class="py-4 overflow-y-auto px-12">

                <div class="prose lg:prose-xl font-sans">

                    <h2>
                        Empowering Young Minds Through Creativity
                    </h2>

                    <p>In a world filled with stories, every child deserves the chance to create their own.
                        Stories are more than just words on a page; they are the foundation of how we dream,
                        learn, and connect.</p>
                    <p>At DearBook, we believe in the power of imagination—whether it’s a
                        child’s vivid dream or a simple idea that sparks a whole new world of possibility. Our
                        mission is to ignite this spark of imagination, blending the boundless creativity of
                        children with the capabilities of modern technology.</p>

                    <p>We believe every child has a unique story to tell, and those stories deserve to be
                        celebrated. Through DearBook, we offer a platform where parents can input a theme or
                        idea, and our AI breathes life into it—creating personalized, illustrated books that
                        reflect the child’s imagination and creativity.</p>

                    <h3>
                        Our Vision
                    </h3>

                    <p>To empower children to become creators, dreamers, and storytellers by offering them a
                        platform where their ideas take flight through personalized, illustrated books. By using
                        cutting-edge AI technology, we ensure every child sees their thoughts, dreams, and
                        emotions transformed into vibrant tales.</p>

                    <h3>
                        Why We Exist
                    </h3>

                    <p> In today’s fast-paced, digital-first world, children grow up surrounded by technology
                        and innovation, but the process of creating stories has often been limited by tools,
                        skills, or access. Books remain timeless, yet the act of storytelling should evolve.
                        Through DearBook, we aim to democratize the creation of books, ensuring that no child is
                        left out of the joy of seeing their creativity realized in tangible form. This platform
                        isn’t just about generating books; it’s about: Inspiring creativity by helping children dream
                        bigger and express themselves.
                        Fostering curiosity through interactive prompts and AI-generated illustrations that
                        introduce children to language, art, and storytelling.
                        Building confidence as children see their ideas and emotions transformed into a finished
                        work of art—something they can hold, share, and be proud of.
                    </p>

                    <h3>
                        Our Commitments
                    </h3>

                    <ul>
                        <li>
                            <b>Child-Centric Design</b>: We build tools that are intuitive, engaging, and safe for young
                            minds.
                            Parents and guardians can guide the creative process, but children are at the heart of every
                            story.
                        </li>
                        <li>
                            <b>Boundless Imagination</b>: No idea is too big or too small. If a child can dream it,
                            DearBook
                            strives to bring it to life, turning simple descriptions into colorful, captivating stories.
                        </li>
                        <li>
                            <b>Ethical Use of AI</b>: We believe that technology should augment creativity, not stifle
                            it. Our
                            AI is designed to reflect the child’s unique ideas and emotions, never overshadowing their
                            voice but empowering it.
                        </li>
                        <li>
                            <b>Inclusivity</b>: Stories are for everyone. We ensure representation and diversity in our
                            illustrations and prompts so that every child sees themselves reflected in the stories we
                            create.
                        </li>
                        <li>
                            <b>Educational Growth</b>: DearBook isn’t just about fun; it’s also about learning. Through
                            storytelling, children develop language skills, visual literacy, and the ability to think
                            critically about the stories they tell and the worlds they create.
                        </li>
                    </ul>


                    <h3>
                        The Process
                    </h3>

                    <ul>
                        <li>
                            <b>Idea First</b>: The journey begins with a simple idea, a character, or a spark of
                            inspiration—a
                            seed planted by parents, teachers, or the child themselves.
                        </li>
                        <li>
                            <b>Interactive Storytelling</b>: Guided prompts lead children to shape their story in their
                            voice.
                            The child’s personality, creativity, and imagination are woven into every page.
                        </li>
                        <li>
                            <b>AI-Powered Art</b>: With just a few clicks, their story is transformed into a beautifully
                            illustrated book, reflecting the spirit of the story and the child’s unique vision.
                        </li>
                        <li>
                            <b>Tangible Outcomes</b>: Children can share their creations digitally with friends and
                            family or
                            receive a printed keepsake—a lasting memory of their creativity.
                        </li>
                    </ul>

                    <h2>The Bigger Picture</h2>

                    <p>
                        We are shaping the storytellers of tomorrow. By giving children ownership of their
                        creativity today, we are planting the seeds for a generation that values art, expression,
                        and innovation. DearBook is more than just a platform—it’s a movement. A movement to reclaim
                        storytelling for children in a way that is modern yet timeless, simple yet profound.
                    </p>

                    <p>
                        In this journey, parents, teachers, and communities are essential partners, supporting
                        children as they explore and experiment. Together, we can create a world where every child’s
                        imagination has a voice, and those voices are celebrated as the stories that shape our
                        collective future.
                    </p>

                    <i>At DearBook, we don’t just tell stories—we empower young minds to create them.</i>

                </div>

            </div>

        </DialogContent>

    </Dialog>

</template>

<script setup lang="ts">

    import {Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger} from "../../@/components/ui/dialog";
    import Manifest from "../icons/Manifest.vue";

</script>
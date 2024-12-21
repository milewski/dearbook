export class AudioManager {

    private collection: Record<string, HTMLAudioElement> = {}

    private current: HTMLAudioElement = null

    public add(name: string, source: string) {

        const audio = new Audio()

        audio.src = source

        this.collection[ name ] = audio

    }

    public async stop() {

        if (this.current) {
            this.current.pause()
            this.current.currentTime = 0
        }

    }

    public async play(name: string, then: () => void) {

        await this.stop()

        const listener = () => {
            this.collection[ name ].removeEventListener('ended', listener)
            then()
        }

        this.collection[ name ].addEventListener('ended', listener)
        this.current = this.collection[ name ]

        await this.collection[ name ].play()

    }

}
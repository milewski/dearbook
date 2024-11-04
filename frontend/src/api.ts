export type BookIndexResource = {
    id: number,
    batch_id: string,
    title: string,
    cover: string,
}

export type BookDetailResource = {
    id: number,
    batch_id: string,
    type: 'book',
    title: string,
    subject: string,
    cover: string,
    backdrop: string,
    paragraphs: Array<{
        illustration: string,
        text: string
    }>
}

export type BooksResponse = {
    data: Array<BookIndexResource>
}

const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
}

const API_HOST = import.meta.env.VITE_API_HOST

export async function fetchBookById(bookId: number, controller: AbortController): Promise<BookDetailResource> {

    return fetch(`https://${ API_HOST }/book/${ bookId }`, { signal: controller.signal, headers })
        .then(response => response.json())
        .then((response: { data: BookDetailResource }) => response.data)

}

export async function bookSearch(term: string, controller: AbortController): Promise<BookIndexResource[]> {

    return fetch(`https://${ API_HOST }/books?term=${ term }`, { signal: controller.signal, headers })
        .then(response => response.json())
        .then((response: BooksResponse) => response.data)

}

export async function booksList(): Promise<BookIndexResource[]> {

    return fetch(`https://${ API_HOST }/books`, { headers })
        .then(response => response.json())
        .then((response: BooksResponse) => response.data)

}

export async function createBook(prompt: string | undefined): Promise<{ id: string }> {

    return fetch(`https://${ API_HOST }/book/create`, {
        method: 'POST',
        headers,
        body: JSON.stringify({ prompt }),
    }).then(response => response.json())

}

export async function checkBatches(ids: string[]): Promise<Record<string, boolean | BookIndexResource>> {

    return fetch(`https://${ API_HOST }/check/batches`, {
        method: 'POST',
        headers,
        body: JSON.stringify({ ids }),
    }).then(response => response.json())

}



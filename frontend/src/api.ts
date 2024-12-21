export type BookId = string;

export type BookIndexResource = {
    id: BookId,
    batch_id: string,
    title: string,
    cover: string,
}

export type BookDetailResource = {
    id: BookId,
    batch_id: string,
    type: 'book',
    title: string,
    synopsis: string,
    synopsis_speech: string | null,
    cover: string,
    backdrop: string,
    paragraphs: Array<{
        illustration: string,
        speech: string | null,
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

export async function fetchBookById(bookId: BookId, controller: AbortController): Promise<BookDetailResource> {

    return fetch(`https://${ API_HOST }/book/${ bookId }`, { signal: controller.signal, headers })
        .then(response => response.json())
        .then((response: { data: BookDetailResource }) => response.data)

}

export async function bookSearch(term: string, controller: AbortController): Promise<BookIndexResource[]> {

    return fetch(`https://${ API_HOST }/books?term=${ term }`, { signal: controller.signal, headers })
        .then(response => response.json())
        .then((response: BooksResponse) => response.data)

}

export async function createBook(prompt: string, wallet: string): Promise<{ id: string }> {

    return fetch(`https://${ API_HOST }/book/create`, {
        method: 'POST',
        headers,
        body: JSON.stringify({ prompt, wallet }),
    }).then(response => response.json())

}

export async function createBookAdvanced(title: string, prompt: string, negative: string, wallet: string): Promise<{ id: string }> {

    return fetch(`https://${ API_HOST }/book/create/advanced`, {
        method: 'POST',
        headers,
        body: JSON.stringify({ title, prompt, negative, wallet }),
    }).then(response => response.json())

}

export async function booksList(): Promise<BookIndexResource[]> {

    return fetch(`https://${ API_HOST }/books`, { headers })
        .then(response => response.json())
        .then((response: BooksResponse) => response.data)

}

export async function checkBatches(ids: string[]): Promise<Record<string, BookIndexResource>> {

    return fetch(`https://${ API_HOST }/check/batches`, {
        method: 'POST',
        headers,
        body: JSON.stringify({ ids }),
    }).then(response => response.json())

}

export async function myBooks(wallet: string): Promise<Record<string, BookIndexResource | true | string>> {

    return fetch(`https://${ API_HOST }/my/books?wallet=${ wallet }`, { headers }).then(response => response.json())

}

export async function deleteBook(id: string, wallet: string): Promise<void> {

    await fetch(`https://${ API_HOST }/book/${ id }/delete`, {
        method: 'POST',
        headers,
        body: JSON.stringify({ wallet }),
    })

}
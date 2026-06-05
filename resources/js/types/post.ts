export type Post = {
    id: number;
    title: string;
    content: string;
    department_id: number | null;
    image_url: string | null;
    type: string;
    created_at: string;
    department?: { id: number; name: string } | null;
    tags?: { id: number; name: string }[];
};

export type Event = Post & {
    start_date: string | null;
    end_date: string | null;
    location: string | null;
};

export type DownloadableFile = {
    id: number;
    folder_id: number;
    name: string;
    file_path: string;
    file_type: string | null;
    file_size: number | null;
    description: string | null;
    order: number;
    created_at: string;
};

export type DownloadableFolder = {
    id: number;
    name: string;
    description: string | null;
    parent_id: number | null;
    order: number;
    files: DownloadableFile[];
    children: DownloadableFolder[];
    created_at: string;
};

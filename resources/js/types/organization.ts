export type DepartmentMember = {
    id: number;

    department_id: number;
    name: string;
    position: string;
    image_url: string | null;
    order: number;
};

export type DepartmentCarousel = {
    id: number;
    title: string;
    description: string;
    button_text: string | null;
    button_url: string | null;
    image_url: string;
    order: number;
    department_id: number | null;
};

export type Department = {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    image_url: string | null;
    content: string | null;
    members: DepartmentMember[];
    carousels?: DepartmentCarousel[];
};

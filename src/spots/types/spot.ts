export type Spot = {
	id: string;
	title?: string | null;
	description?: string | null;
	latitude?: number | null;
	longitude?: number | null;
	payload?: string | null;
	redirection?: string | null;
	referenced?: boolean;
	configured?: boolean;
	createdAt: Date;
	updatedAt: Date;
};

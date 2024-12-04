export type Spot = {
	id: string;
	name: string | null;
	description: string | null;
	latitude: number | null;
	longitude: number | null;
	payload: string | null;
	websiteTarget: string | null;
	directAccessToWebsiteTarget: boolean;
	configured: boolean;
	public: boolean;
	createdAt: Date;
	updatedAt: Date;
};

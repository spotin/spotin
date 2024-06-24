import { UserRole } from '@prisma/client';

export type User = {
	id: string;
	username: string;
	email: string;
	password: string;
	role: UserRole;
	enabled: boolean;
	createdAt: Date;
	updatedAt: Date;
};

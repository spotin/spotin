import { UserRole } from '@/users/enums/user-role';

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

import { UserRole } from '@/users/enums/user-role';

export type JwtPayload = {
	username: string;
	email: string;
	role: UserRole;
};

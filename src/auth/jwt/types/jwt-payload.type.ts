import { UserRole } from '@/users/enums/user-role';

export type JwtPayload = {
	sub: string;
	username: string;
	email: string;
	role: UserRole;
};

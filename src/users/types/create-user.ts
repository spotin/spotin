import { User } from '@/users/types/user';

export type CreateUser = Omit<
	User,
	'id' | 'password' | 'createdAt' | 'updatedAt'
>;

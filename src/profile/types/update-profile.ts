import { Profile } from '@/profile/types/profile';

export type UpdateProfile = Partial<Omit<Profile, 'email' | 'createdAt'>> & {
	currentPassword: string;
	newPassword?: string;
};

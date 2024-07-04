import { Profile } from '@/profile/types/profile';

export type UpdateProfile = Partial<Profile> & {
	currentPassword: string;
	newPassword?: string;
};

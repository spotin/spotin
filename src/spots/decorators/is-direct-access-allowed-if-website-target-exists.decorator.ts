import { Spot } from '@/spots/types/spot';
import {
	registerDecorator,
	ValidationOptions,
	ValidationArguments,
} from 'class-validator';

/**
 * Custom decorator to validate that directAccessToWebsiteTarget can be true only if websiteTarget is not null.
 */
export function IsDirectAccessAllowedIfWebsiteTargetExists(
	validationOptions?: ValidationOptions,
) {
	return function (object: Spot, propertyName: string): void {
		registerDecorator({
			name: 'isDirectAccessAllowedIfWebsiteTargetExists',
			target: object.constructor,
			propertyName: propertyName,
			options: validationOptions,
			validator: {
				validate(
					directAccessToWebsiteTarget: boolean,
					args: ValidationArguments,
				) {
					const { websiteTarget } = args.object as Spot;
					if (
						directAccessToWebsiteTarget === true &&
						(websiteTarget === null || websiteTarget === undefined)
					) {
						return false;
					}
					return true;
				},
				defaultMessage() {
					return `directAccessToWebsiteTarget can only be true if websiteTarget is not null or undefined.`;
				},
			},
		});
	};
}
